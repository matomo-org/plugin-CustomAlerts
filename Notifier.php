<?php

/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id$
 *
 */

namespace Piwik\Plugins\CustomAlerts;

use Piwik\Mail;
use Piwik\Period;
use Piwik\Piwik;
use Piwik\Date;
use Piwik\Plugin\Manager as PluginManager;
use Piwik\Plugins\MobileMessaging\API as APIMobileMessaging;
use Piwik\Site;
use Piwik\View;
use Piwik\Plugins\UsersManager\API as UsersManagerApi;

/**
 *
 */
class Notifier extends \Piwik\Plugin
{
    protected function getTriggeredAlerts($period, $idSite)
    {
        $now    = $this->getToday()->getDatetime();

        $model  = new Model();
        $alerts = $model->getTriggeredAlertsForPeriod($period, $now, false);

        return array_filter($alerts, function ($alert) use ($idSite) {
            return $idSite && (int) $alert['idsite'] === (int) $idSite && empty($alert['ts_last_sent']);
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

        $alertsPerEmail = $this->groupAlertsPerEmailRecipient($triggeredAlerts);
        foreach ($alertsPerEmail as $email => $alerts) {
            $this->sendAlertsPerEmailToRecipient($alerts, new Mail(), $email, $period, $idSite);
        }

        $alertsPerSms = $this->groupAlertsPerSmsRecipient($triggeredAlerts);
        foreach ($alertsPerSms as $phoneNumber => $alerts) {
            $this->sendAlertsPerSmsToRecipient($alerts, APIMobileMessaging::getInstance(), $phoneNumber);
        }

        foreach($triggeredAlerts as $triggeredAlert) {
            $this->markAlertAsSent($triggeredAlert);
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
        $timestamp = Date::now()->getDatetime();

        $model = new Model();
        $model->markTriggeredAlertAsSent($triggeredAlert['idtriggered'], $timestamp);
    }

    protected function getEmailAddressFromLogin($login)
    {
        if (empty($login)) {
            return '';
        }

        $user = UsersManagerApi::getInstance()->getUser($login);

        if (empty($user) || empty($user['email'])) {
            return '';
        }

        return $user['email'];
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

        $controller = new Controller();
        $content = $controller->formatAlerts($alerts, 'sms');
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

        $mail->setDefaultFromPiwik();
        $mail->addTo($recipient);
        $mail->setSubject(Piwik::translate('CustomAlerts_MailAlertSubject', array($websiteName, $prettyDate)));

        $controller = new Controller();

        $viewHtml = new View('@CustomAlerts/alertHtmlMail');
        $viewHtml->assign('triggeredAlerts', $controller->formatAlerts($alerts, 'html'));
        $mail->setBodyHtml($viewHtml->render());

        $viewText = new View('@CustomAlerts/alertTextMail');
        $viewText->assign('triggeredAlerts', $controller->formatAlerts($alerts, 'text'));
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
        return Date::now();
    }

    protected function getPrettyDateForSite($period, $idSite)
    {
        $timezone = Site::getTimezoneFor($idSite);

        $date = $this->getToday();
        $date = Date::factory($date->getDatetime(), $timezone);
        // we ran the alerts for the period before...
        $date = $date->subPeriod(1, $period);

        // also make sure if period is month to display "2014-01" and not "2014-01-31"
        $period     = Period::factory($period, $date);
        $prettyDate = $period->getLocalizedLongString();

        return $prettyDate;
    }

}
?>
