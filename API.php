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

use Piwik\Period;
use Piwik\Db;
use Piwik\Piwik;
use Piwik\Plugins\MobileMessaging\API as APIMobileMessaging;
use Exception;

/**
 *
 * @package Piwik_Alerts
 * @method static \Piwik\Plugins\CustomAlerts\API getInstance()
 */
class API extends \Piwik\Plugin\API
{
    /**
     * Returns a single Alert
     *
     * @param int $idAlert
     *
     * @return array
     */
	public function getAlert($idAlert)
	{
        $alert = $this->getModel()->getAlert($idAlert);

        $this->checkUserHasPermissionForAlert($idAlert, $alert);

        return $alert;
    }

    /**
     * Returns the Alerts that are defined on the idSites given.
     * If no value is given, all Alerts for the current user will
     * be returned.
     *
     * @param array $idSites
     * @return array
     */
	public function getAlerts($idSites)
	{
        if (empty($idSites)) {
            return array();
        }

        Piwik::checkUserHasViewAccess($idSites);

        return $this->getModel()->getAlerts($idSites);
	}

    /**
     * Creates an Alert for given website(s).
     *
     * @param string $name
     * @param mixed $idSites
     * @param string $period
     * @param bool $emailMe
     * @param array $additionalEmails
     * @param array $phoneNumbers
     * @param string $metric (nb_uniq_visits, sum_visit_length, ..)
     * @param string $metricCondition
     * @param float $metricValue
     * @param string $report
     * @param string $reportCondition
     * @param string $reportValue
     * @internal param bool $enableEmail
     * @return int ID of new Alert
     */
	public function addAlert($name, $idSites, $period, $emailMe, $additionalEmails, $phoneNumbers, $metric, $metricCondition, $metricValue, $report, $reportCondition = '', $reportValue = '')
	{
        if (!is_array($idSites)) {
            $idSites = array($idSites);
        }

        Piwik::checkUserHasViewAccess($idSites);

        $additionalEmails = $this->checkAdditionalEmails($additionalEmails);
        $phoneNumbers     = $this->checkPhoneNumbers($phoneNumbers);
        $this->checkPeriod($period);

        $emailMe = $emailMe ? 1 : 0;

        return $this->getModel()->addAlert($name, $idSites, $period, $emailMe, $additionalEmails, $phoneNumbers, $metric, $metricCondition, $metricValue, $report, $reportCondition, $reportValue);
	}

    /**
     * Edits an Alert for given website(s).
     *
     * @param $idAlert
     * @param string $name Name of Alert
     * @param mixed $idSites Single int or array of ints of idSites.
     * @param string $period Period the alert is defined on.
     * @param bool $emailMe
     * @param array $additionalEmails
     * @param array $phoneNumbers
     * @param string $metric (nb_uniq_visits, sum_visit_length, ..)
     * @param string $metricCondition
     * @param float $metricValue
     * @param string $report
     * @param string $reportCondition
     * @param string $reportValue
     *
     * @internal param bool $enableEmail
     * @return boolean
     */
	public function editAlert($idAlert, $name, $idSites, $period, $emailMe, $additionalEmails, $phoneNumbers, $metric, $metricCondition, $metricValue, $report, $reportCondition = '', $reportValue = '')
	{
        // make sure alert exists and user has permission to read
        $this->getAlert($idAlert);

        if (!is_array($idSites)) {
            $idSites = array($idSites);
        }

        Piwik::checkUserHasViewAccess($idSites);

        $additionalEmails = $this->checkAdditionalEmails($additionalEmails);
        $phoneNumbers     = $this->checkPhoneNumbers($phoneNumbers);
        $this->checkPeriod($period);

        $emailMe = $emailMe ? 1 : 0;

        return $this->getModel()->editAlert($idAlert, $name, $idSites, $period, $emailMe, $additionalEmails, $phoneNumbers, $metric, $metricCondition, $metricValue, $report, $reportCondition, $reportValue);
	}

    /**
     * Delete alert by id.
     *
     * @param int $idAlert
     * @throws \Exception
     */
	public function deleteAlert($idAlert)
	{
        $alert = $this->getAlert($idAlert);

        if (empty($alert)) {
            throw new Exception(Piwik::translate('CustomAlerts_AlertDoesNotExist', $idAlert));
        }

        $this->checkUserHasPermissionForAlert($idAlert, $alert);

        $this->getModel()->deleteAlert($idAlert);
	}

    /**
     * Get all alerts
     *
     * @param string $period
     * @return array
     * @throws \Exception
     */
	public function getAllAlerts($period)
	{
        Piwik::checkUserIsSuperUser();

        if (!$this->isValidPeriod($period)) {
            throw new Exception("Invalid period given.");
        }

        return $this->getModel()->getAllAlerts($period);
	}

    /**
     * Get triggered alerts.
     *
     * @param int $idAlert
     * @param int $idSite
     */
    public function triggerAlert($idAlert, $idSite)
    {
        $alert = $this->getAlert($idAlert);

        if (empty($alert)) {
            throw new Exception(Piwik::translate('CustomAlerts_AlertDoesNotExist', $idAlert));
        }

        $this->checkUserHasPermissionForAlert($idAlert, $alert);

        $this->getModel()->triggerAlert($idAlert, $idSite);
    }

    /**
     * Get triggered alerts.
     *
     * @param string $period
     * @param string $date
     * @param string $login
     * @return array
     */
	public function getTriggeredAlerts($period, $date, $login)
	{
        Piwik::checkUserIsSuperUserOrTheUser($login);

        $this->checkPeriod($period);

        return $this->getModel()->getTriggeredAlerts($period, $date, $login);
	}

    private function getModel()
    {
        return new Model();
    }

    private function checkAdditionalEmails($additionalEmails)
    {
        foreach ($additionalEmails as &$email) {

            $email = trim($email);
            if (empty($email)) {
                $email = false;
            } elseif (!Piwik::isValidEmailString($email)) {
                throw new \Exception(Piwik::translate('UsersManager_ExceptionInvalidEmail') . ' (' . $email . ')');
            }

        }

        $additionalEmails = array_filter($additionalEmails);

        return $additionalEmails;
    }

    private function checkPhoneNumbers($phoneNumbers)
    {
        $availablePhoneNumbers = APIMobileMessaging::getInstance()->getActivatedPhoneNumbers();

        foreach ($phoneNumbers as $key => &$phoneNumber) {

            $phoneNumber = trim($phoneNumber);

            if (!in_array($phoneNumber, $availablePhoneNumbers)) {
                unset($phoneNumbers[$key]);
            }
        }

        return array_values($phoneNumbers);
    }

    private function checkPeriod($period)
    {
        if (!$this->isValidPeriod($period)) {
            throw new Exception(Piwik::translate('CustomAlerts_InvalidPeriod'));
        }
    }

    private function isValidPeriod($period)
    {
        return in_array($period, array('day', 'week', 'month', 'year'));
    }

    /**
     * @param $idAlert
     * @param $alert
     * @throws \Exception
     */
    private function checkUserHasPermissionForAlert($idAlert, $alert)
    {
        if (!Piwik::isUserIsSuperUserOrTheUser($alert['login'])) {
            throw new Exception(Piwik::translate('CustomAlerts_AccessException', $idAlert));
        }
    }
}
?>