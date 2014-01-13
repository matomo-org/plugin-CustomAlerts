<?php

/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id$
 *
 * @category Piwik_Plugins
 * @package Piwik_Alerts
 */

namespace Piwik\Plugins\CustomAlerts;

use Piwik\Mail;
use Piwik\Period;
use Piwik\Piwik;
use Piwik\Date;
use Piwik\Plugin\Manager as PluginManager;
use Piwik\Plugins\API\ProcessedReport;
use Piwik\Plugins\MobileMessaging\API as APIMobileMessaging;
use Piwik\Site;
use Piwik\View;
use Piwik\Plugins\UsersManager\API as UsersManagerApi;

/**
 *
 * @package Piwik_CustomAlerts
 */
class Notifier extends \Piwik\Plugin
{
    protected function getTriggeredAlerts($period, $idSite)
    {
        $api    = API::getInstance();
        $alerts = $api->getTriggeredAlerts($period, $this->getToday(), false);

        return array_filter($alerts, function ($alert) use ($idSite) {
            return $alert['idsite'] == $idSite && empty($alert['ts_last_sent']);
        });
    }

    /**
     * Sends a list of the triggered alerts to all recipients.
     *
     * @param string $period
     * @param int    $idSite
     */
	public function sendNewAlerts($period, $idSite)
	{
		$triggeredAlerts = $this->getTriggeredAlerts($period, $idSite);

        foreach($triggeredAlerts as $triggeredAlert) {
            $this->markAlertAsSent($triggeredAlert);
        }

        $alertsPerEmail = $this->groupAlertsPerEmailRecipient($triggeredAlerts);
        foreach ($alertsPerEmail as $email => $alerts) {
            $this->sendAlertsPerEmailToRecipient($alerts, new Mail(), $email, $period, $idSite);
        }

        $alertsPerSms = $this->groupAlertsPerSmsRecipient($triggeredAlerts);
        foreach ($alertsPerSms as $phoneNumber => $alerts) {
            $this->sendAlertsPerSmsToRecipient($alerts, APIMobileMessaging::getInstance(), $phoneNumber);
        }
	}

    private function groupAlertsPerSmsRecipient($triggeredAlerts)
    {
        $recipients = array();

        foreach($triggeredAlerts as $triggeredAlert) {

            $phoneNumbers = $triggeredAlert['phone_numbers'];

            if (empty($phoneNumbers)) {
                $phoneNumbers = array();
            }

            foreach ($phoneNumbers as $phoneNumber) {
                if (!array_key_exists($phoneNumber, $recipients)) {
                    $recipients[$phoneNumber] = array();
                }

                $recipients[$phoneNumber][] = $triggeredAlert;
            }
        }

        return $recipients;
    }

    private function groupAlertsPerEmailRecipient($triggeredAlerts)
    {
        $alertsPerEmail = array();

        foreach($triggeredAlerts as $triggeredAlert) {

            $emails = $this->getEmailRecipientsForAlert($triggeredAlert);

            foreach ($emails as $mail) {
                if (!array_key_exists($mail, $alertsPerEmail)) {
                    $alertsPerEmail[$mail] = array();
                }

                $alertsPerEmail[$mail][] = $triggeredAlert;
            }
        }

        return $alertsPerEmail;
    }

    protected function markAlertAsSent($triggeredAlert)
    {
        $timestamp = Date::now()->getTimestamp();

        $model = new Model();
        $model->markTriggeredAlertAsSent($triggeredAlert, $timestamp);
    }

    protected function getEmailAddressFromLogin($login)
    {
        if (empty($login)) {
            return '';
        }

        if ($login == Piwik::getSuperUserLogin()) {
            return Piwik::getSuperUserEmail();
        }

        $user = UsersManagerApi::getInstance()->getUser($login);

        if (empty($user) || empty($user['email'])) {
            return '';
        }

        return $user['email'];
    }

    /**
     * Returns the Alerts that were triggered in $format.
     *
     * @param array $triggeredAlerts
     * @param string $format Can be 'html' or 'tsv'
     * @throws \Exception
     * @return string
     */
	protected function formatAlerts($triggeredAlerts, $format)
	{
		switch ($format) {
			case 'html':
				$view = new View('@CustomAlerts/htmlTriggeredAlerts');
				$view->triggeredAlerts = $this->enrichTriggeredAlerts($triggeredAlerts);

				return $view->render();

            case 'sms':

                $view = new View('@CustomAlerts/smsTriggeredAlerts');
                $view->triggeredAlerts = $this->enrichTriggeredAlerts($triggeredAlerts);

                return $view->render();

			case 'text':

                $view = new View('@CustomAlerts/textTriggeredAlerts');
                $view->triggeredAlerts = $this->enrichTriggeredAlerts($triggeredAlerts);

                return $view->render();
		}

        throw new \Exception('Unsupported format');
	}

    protected function enrichTriggeredAlerts($triggeredAlerts)
    {
        foreach ($triggeredAlerts as &$alert) {
            list($module, $action) = explode('.', $alert['report']);
            $idSite = $alert['idsite'];
            $metric = $alert['metric'];

            $processedReport = new ProcessedReport();

            $alert['reportName']   = null;
            $alert['dimension']    = null;
            $alert['reportMetric'] = $processedReport->translateMetric($metric, $idSite, $module, $action);
            $alert['reportConditionName'] = null;

            $metadata = $processedReport->getMetadata($idSite, $module, $action);
            if (!empty($metadata)) {
                $report = array_shift($metadata);
                $alert['reportName'] = $report['name'];
                $alert['dimension']  = !empty($report['dimension']) ? $report['dimension'] : null;

                $conditionTranslation = array_search($alert['report_condition'], Processor::getGroupConditions(), true);
                $alert['reportConditionName'] = $conditionTranslation ? Piwik::translate($conditionTranslation) : null;
            }
        }

        return $triggeredAlerts;
    }

    /**
     * @param array  $alerts
     * @param APIMobileMessaging $mobileMessagingAPI
     * @param string $phoneNumber
     */
    protected function sendAlertsPerSmsToRecipient($alerts, $mobileMessagingAPI, $phoneNumber)
    {
        if (empty($phoneNumber) || empty($alerts)) {
            return;
        }

        if (!PluginManager::getInstance()->isPluginActivated('MobileMessaging')) {
            return;
        }

        $content = $this->formatAlerts($alerts, 'sms');
        $subject = Piwik::translate('CustomAlerts_SmsAlertFromName');

        $mobileMessagingAPI->sendSMS(
            $content,
            $phoneNumber,
            $subject
        );
    }

    /**
     * @param array $alerts
     * @param Mail $mail
     * @param string[] $recipient Email addresses
     * @param $period
     * @param $idSite
     */
    protected function sendAlertsPerEmailToRecipient($alerts, Mail $mail, $recipient, $period, $idSite)
    {
        if (empty($recipient) || empty($alerts)) {
            return;
        }

        $prettyDate  = $this->getPrettyDateForSite($period, $idSite);
        $websiteName = Site::getNameFor($idSite);

        $mail->addTo($recipient);
        $mail->setSubject(Piwik::translate('CustomAlerts_MailAlertSubject', array($websiteName, $prettyDate)));

        $viewHtml = new View('@CustomAlerts/alertHtmlMail');
        $viewHtml->assign('triggeredAlerts', $this->formatAlerts($alerts, 'html'));
        $mail->setBodyHtml($viewHtml->render());

        $viewText = new View('@CustomAlerts/alertTextMail');
        $viewText->assign('triggeredAlerts', $this->formatAlerts($alerts, 'text'));
        $viewText->setContentType('text/plain');
        $mail->setBodyText($viewText->render());

        $mail->send();
    }

    /**
     * @param $triggeredAlert
     * @return array
     */
    private function getEmailRecipientsForAlert($triggeredAlert)
    {
        $recipients = $triggeredAlert['additional_emails'];

        if (empty($recipients)) {
            $recipients = array();
        }

        if ($triggeredAlert['email_me']) {
            $recipients[] = $this->getEmailAddressFromLogin($triggeredAlert['login']);
        }

        return $recipients;
    }

    protected function getToday()
    {
        return Date::today();
    }

    protected function getPrettyDateForSite($period, $idSite)
    {
        $timezone = Site::getTimezoneFor($idSite);

        $date = $this->getToday();
        $date = $date->setTimezone($timezone);
        // we ran the alerts for the period before...
        $date = $date->subPeriod(1, $period);

        // also make sure if period is month to display "2014-01" and not "2014-01-31"
        $period     = Period::factory($period, $date);
        $prettyDate = $period->getLocalizedLongString();

        return $prettyDate;
    }

}
?>
