<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 */

namespace Piwik\Plugins\CustomAlerts;

use Exception;
use Piwik\Common;
use Piwik\Piwik;
use Piwik\Site;

/**
 *
 * @method static \Piwik\Plugins\CustomAlerts\API getInstance()
 */
class API extends \Piwik\Plugin\API
{
    private $validator;
    /**
     * @var Processor
     */
    private $processor;

    public function __construct(Processor $processor, Validator $validator)
    {
        $this->validator = $validator;
        $this->processor = $processor;
    }

    /**
     * Calculates the alert value for each site for the given days/weeks/months in past. If the period of the alert is
     * weeks and subPeriodN is "7" it will return the value for the week 7 weeks ago. Set subPeriodN to "0" to test the
     * current day/week/month.
     *
     * @param int $idAlert
     * @param int $subPeriodN
     *
     * @return array
     */
    public function getValuesForAlertInPast($idAlert, $subPeriodN)
    {
        $alert = $this->getAlert($idAlert);

        $values = [];
        foreach ($alert['id_sites'] as $idSite) {
            $values[] = array(
                'idSite' => (int)$idSite,
                'value'  => $this->processor->getValueForAlertInPast($alert, $idSite, (int)$subPeriodN)
            );
        }

        return $values;
    }

    /**
     * Returns a single alert.
     *
     * @param int $idAlert
     *
     * @return array
     * @throws \Exception In case alert does not exist or user has no permission to access alert.
     */
    public function getAlert($idAlert)
    {
        $alert = $this->getModel()->getAlert($idAlert);

        if (empty($alert)) {
            throw new Exception(Piwik::translate('CustomAlerts_AlertDoesNotExist', $idAlert));
        }

        $this->validator->checkUserHasPermissionForAlert($alert);

        return $alert;
    }

    private function getModel()
    {
        return new Model();
    }

    /**
     * Returns the Alerts that are defined on the idSites given.
     *
     * @param array $idSites
     * @param bool  $ifSuperUserReturnAllAlerts
     *
     * @return array
     */
    public function getAlerts($idSites, $ifSuperUserReturnAllAlerts = false)
    {
        $idSites = Site::getIdSitesFromIdSitesString($idSites);

        if (empty($idSites)) {
            return [];
        }

        Piwik::checkUserHasViewAccess($idSites);

        if (Piwik::hasUserSuperUserAccess() && $ifSuperUserReturnAllAlerts) {
            $login = false;
        } else {
            $login = Piwik::getCurrentUserLogin();
        }

        $alerts = $this->getModel()->getAlerts($idSites, $login);

        return $alerts;
    }

    /**
     * Creates an Alert for given website(s).
     *
     * @param string      $name
     * @param mixed       $idSites
     * @param string      $period
     * @param bool        $emailMe
     * @param array       $additionalEmails
     * @param array       $phoneNumbers
     * @param string      $metric (nb_uniq_visits, sum_visit_length, ..)
     * @param string      $metricCondition
     * @param float       $metricValue
     * @param string      $reportUniqueId
     * @param int         $comparedTo
     * @param bool|string $reportCondition
     * @param bool|string $reportValue
     * @return int ID of new Alert
     */
    public function addAlert($name, $idSites, $period, $emailMe, $additionalEmails, $phoneNumbers, $metric, $metricCondition, $metricValue, $comparedTo, $reportUniqueId, $reportCondition = false, $reportValue = false)
    {
        $idSites          = Site::getIdSitesFromIdSitesString($idSites);
        $additionalEmails = $this->filterAdditionalEmails($additionalEmails);
        $phoneNumbers     = $this->filterPhoneNumbers($phoneNumbers);

        $this->checkAlert($idSites, $name, $period, $additionalEmails, $metricCondition, $metric, $comparedTo, $reportCondition, $reportUniqueId);

        $name  = Common::unsanitizeInputValue($name);
        $login = Piwik::getCurrentUserLogin();

        if (empty($reportCondition) || empty($reportValue)) {
            $reportCondition = null;
            $reportValue     = null;
        }

        $metricValue = Common::forceDotAsSeparatorForDecimalPoint((float)$metricValue);

        return $this->getModel()->createAlert($name, $idSites, $login, $period, $emailMe, $additionalEmails, $phoneNumbers, $metric, $metricCondition, $metricValue, $comparedTo, $reportUniqueId, $reportCondition, $reportValue);
    }

    private function filterAdditionalEmails($additionalEmails)
    {
        if (empty($additionalEmails)) {
            return array();
        }

        foreach ($additionalEmails as &$email) {

            $email = trim($email);
            if (empty($email)) {
                $email = false;
            }
        }

        return array_filter($additionalEmails);
    }

    private function filterPhoneNumbers($phoneNumbers)
    {
        $availablePhoneNumbers = (new \Piwik\Plugins\MobileMessaging\Model())->getActivatedPhoneNumbers(Piwik::getCurrentUserLogin());

        foreach ($phoneNumbers as $key => &$phoneNumber) {

            $phoneNumber = trim($phoneNumber);

            if (!in_array($phoneNumber, $availablePhoneNumbers)) {
                unset($phoneNumbers[$key]);
            }
        }

        return array_values($phoneNumbers);
    }

    private function checkAlert($idSites, $name, $period, $additionalEmails, $metricCondition, $metricValue, $comparedTo, $reportCondition, $reportUniqueId)
    {
        Piwik::checkUserHasViewAccess($idSites);

        $this->validator->checkName($name);
        $this->validator->checkPeriod($period);
        $this->validator->checkComparedTo($period, $comparedTo);
        $this->validator->checkMetricCondition($metricCondition);
        $this->validator->checkReportCondition($reportCondition);

        foreach ($idSites as $idSite) {
            $this->validator->checkApiMethodAndMetric($idSite, $reportUniqueId, $metricValue);
        }

        $this->validator->checkAdditionalEmails($additionalEmails);
    }

    /**
     * Edits an Alert for given website(s).
     *
     * @param             $idAlert
     * @param string      $name    Name of Alert
     * @param mixed       $idSites Single int or array of ints of idSites.
     * @param string      $period  Period the alert is defined on.
     * @param bool        $emailMe
     * @param array       $additionalEmails
     * @param array       $phoneNumbers
     * @param string      $metric  (nb_uniq_visits, sum_visit_length, ..)
     * @param string      $metricCondition
     * @param float       $metricValue
     * @param string      $reportUniqueId
     * @param int         $comparedTo
     * @param bool|string $reportCondition
     * @param bool|string $reportValue
     *
     * @return boolean
     */
    public function editAlert($idAlert, $name, $idSites, $period, $emailMe, $additionalEmails, $phoneNumbers, $metric, $metricCondition, $metricValue, $comparedTo, $reportUniqueId, $reportCondition = false, $reportValue = false)
    {
        // make sure alert exists and user has permission to read
        $this->getAlert($idAlert);

        $idSites          = Site::getIdSitesFromIdSitesString($idSites);
        $additionalEmails = $this->filterAdditionalEmails($additionalEmails);
        $phoneNumbers     = $this->filterPhoneNumbers($phoneNumbers);

        $this->checkAlert($idSites, $name, $period, $additionalEmails, $metricCondition, $metric, $comparedTo, $reportCondition, $reportUniqueId);

        $name = Common::unsanitizeInputValue($name);

        if (empty($reportCondition) || empty($reportValue)) {
            $reportCondition = null;
            $reportValue     = null;
        }

        $metricValue = Common::forceDotAsSeparatorForDecimalPoint((float)$metricValue);

        return $this->getModel()->updateAlert($idAlert, $name, $idSites, $period, $emailMe, $additionalEmails, $phoneNumbers, $metric, $metricCondition, $metricValue, $comparedTo, $reportUniqueId, $reportCondition, $reportValue);
    }

    /**
     * Delete alert by id.
     *
     * @param int $idAlert
     * @throws \Exception
     */
    public function deleteAlert($idAlert)
    {
        // make sure alert exists and user has permission to read
        $this->getAlert($idAlert);

        $this->getModel()->deleteAlert($idAlert);
    }

    /**
     * Get triggered alerts.
     *
     * @param int[] idSites
     *
     * @return array
     */
    public function getTriggeredAlerts($idSites)
    {
        if (empty($idSites)) {
            return array();
        }

        $idSites = Site::getIdSitesFromIdSitesString($idSites);
        Piwik::checkUserHasViewAccess($idSites);

        $login = Piwik::getCurrentUserLogin();

        return $this->getModel()->getTriggeredAlerts($idSites, $login);
    }

}
