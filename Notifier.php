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
use Piwik\Piwik;
use Piwik\DataTable;
use Piwik\Date;
use Piwik\Plugins\MobileMessaging\API as APIMobileMessaging;
use Piwik\Translate;
use Piwik\View;
use Piwik\Db;
use Piwik\Plugins\UsersManager\API as UsersManagerApi;
use Piwik\Plugins\API\API as MetadataApi;

/**
 *
 * @package Piwik_CustomAlerts
 */
class Notifier extends \Piwik\Plugin
{
    protected function getTriggeredAlerts($period)
    {
        $api = API::getInstance();

        return $api->getTriggeredAlerts($period, Date::today(), false);
    }

	/**
	 * Sends a list of the triggered alerts to
	 * $recipient.
	 *
	 * @param string $period
	 */
	public function sendNewAlerts($period)
	{
		$triggeredAlerts = $this->getTriggeredAlerts($period);

        $alertsPerEmail = array();
        $alertsPerSms   = array();
		foreach($triggeredAlerts as $triggeredAlert) {
            $emails = $this->getEmailRecipientsForAlert($triggeredAlert);

            foreach ($emails as $mail) {
                if (!array_key_exists($mail, $alertsPerEmail)) {
                    $alertsPerEmail[$mail] = array();
                }

                $alertsPerEmail[$mail][] = $triggeredAlert;
            }

            $phoneNumbers = $triggeredAlert['phone_numbers'];

            if (empty($phoneNumbers)) {
                $phoneNumbers = array();
            }
            foreach ($phoneNumbers as $phoneNumber) {
                if (!array_key_exists($phoneNumber, $alertsPerSms)) {
                    $alertsPerSms[$phoneNumber] = array();
                }

                $alertsPerSms[$phoneNumber][] = $triggeredAlert;
            }

		}

        foreach ($alertsPerEmail as $email => $alerts) {
            $this->sendAlertsPerEmailToRecipient($alerts, new Mail(), $email);
        }

        foreach ($alertsPerSms as $phoneNumber => $alerts) {
            $this->sendAlertsPerSmsToRecipient($alerts, APIMobileMessaging::getInstance(), $phoneNumber);
        }
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
        $lang = Translate::getLanguageLoaded();

        foreach ($triggeredAlerts as &$alert) {
            list($module, $action) = explode('.', $alert['report']);

            $metadata = MetadataApi::getInstance()->getMetadata($alert['idsite'], $module, $action, array(), $lang);

            if (!empty($metadata)) {
                $report = array_shift($metadata);

                $apiMethod = $report['module'] . '.' . $report['action'];

                if ($apiMethod == $alert['report']) {
                    $alert['reportName']   = $report['name'];
                    $alert['reportMetric'] = $report['metrics'][$alert['metric']];
                }
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

        $content = $this->formatAlerts($alerts, 'sms');
        $subject = Piwik::translate('CustomAlerts_SmsAlertFromName');

        $mobileMessagingAPI->sendSMS(
            $content,
            $phoneNumber,
            $subject
        );
    }

    /**
     * @param array  $alerts
     * @param Mail $mail
     * @param string[] $recipients Email addresses
     */
    protected function sendAlertsPerEmailToRecipient($alerts, Mail $mail, $recipients)
    {
        if (empty($recipients) || empty($alerts)) {
            return;
        }

        $mail->addTo($recipients);
        $mail->setSubject('Piwik alert [' . Date::today() . ']');

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

}
?>
