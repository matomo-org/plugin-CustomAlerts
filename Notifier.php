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
use Piwik\View;
use Piwik\Db;
use Piwik\Plugins\UsersManager\API as UsersManagerApi;

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

        $alertsPerLogin = array();
		foreach($triggeredAlerts as $triggeredAlert) {
            $login = $triggeredAlert['login'];

            if (!array_key_exists($login, $alertsPerLogin)) {
                $alertsPerLogin[$login] = array();
            }

            $alertsPerLogin[$login][] = $triggeredAlert;
		}

        foreach ($alertsPerLogin as $login => $alerts) {
            $recipient = $this->getEmailAddressFromLogin($login);
            $this->sendAlertsPerEmailToRecipient($alerts, new Mail(), $recipient);
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
				$view->triggeredAlerts = $triggeredAlerts;

				return $view->render();

			case 'tsv':
				$tsv = '';
				$showedTitle = false;
				foreach ($triggeredAlerts as $alert) {
					if (!$showedTitle) {
						$showedTitle = true;
						$tsv .= implode("\t", array_keys($alert)) . "\n";
					}
					$tsv .= implode("\t", array_values($alert)) . "\n";
				}

				return $tsv;
		}

        throw new \Exception('Unsupported format');
	}

    /**
     * @param array  $alerts
     * @param Mail $mail
     * @param string $recipient Email address
     */
    protected function sendAlertsPerEmailToRecipient($alerts, Mail $mail, $recipient)
    {
        if (empty($recipient) || empty($alerts)) {
            return;
        }

        $mail->addTo($recipient);
        $mail->setSubject('Piwik alert [' . Date::today() . ']');

        $viewHtml = new View('@CustomAlerts/alertHtmlMail');
        $viewHtml->assign('triggeredAlerts', $this->formatAlerts($alerts, 'html'));
        $mail->setBodyHtml($viewHtml->render());

        $viewText = new View('@CustomAlerts/alertTextMail');
        $viewText->assign('triggeredAlerts', $this->formatAlerts($alerts, 'tsv'));
        $viewText->setContentType('text/plain');
        $mail->setBodyText($viewText->render());

        $mail->send();
    }

}
?>
