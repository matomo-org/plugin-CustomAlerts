<?php

/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id$
 *
 */

namespace Piwik\Plugins\CustomAlerts;

use Exception;
use Piwik\Common;
use Piwik\Context;
use Piwik\Period;
use Piwik\Piwik;
use Piwik\Plugins\API\ProcessedReport;
use Piwik\Plugins\MobileMessaging\API as APIMobileMessaging;

/**
 *
 */
class Validator
{
    /**
     * @var ProcessedReport
     */
    private $processedReport;

    public function __construct(ProcessedReport $processedReport)
    {
        $this->processedReport = $processedReport;
    }

    public function checkAdditionalEmails($additionalEmails)
    {
        foreach ($additionalEmails as $email) {
            if (!Piwik::isValidEmailString($email)) {
                throw new \Exception(Piwik::translate('UsersManager_ExceptionInvalidEmail') . ' (' . $email . ')');
            }
        }
    }

    public function filterPhoneNumbers($phoneNumbers)
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

    public function checkPeriod($period)
    {
        if (!$this->isValidPeriod($period)) {
            throw new Exception(Piwik::translate('CustomAlerts_InvalidPeriod'));
        }
    }

    public function isValidPeriod($period)
    {
        return in_array($period, array('day', 'week', 'month'));
    }

    public function checkName($name)
    {
        if (empty($name)) {
            throw new Exception(Piwik::translate("General_PleaseSpecifyValue", "name"));
        }

        if (Common::mb_strlen($name) > 100) {
            throw new Exception(Piwik::translate("CustomAlerts_ParmeterIsTooLong", array(Piwik::translate('General_Name'), 100)));
        }
    }

    public function isValidComparableDate($period, $comparedToDate)
    {
        $dates = Processor::getComparablesDates();
        if (!array_key_exists($period, $dates)) {
            return false;
        }

        return in_array($comparedToDate, array_values($dates[$period]));
    }

    public function isValidGroupCondition($condition)
    {
        $conditions = Processor::getGroupConditions();
        $conditions = array_values($conditions);

        return in_array($condition, $conditions);
    }

    public function isValidMetricCondition($condition)
    {
        $conditions = Processor::getMetricConditions();
        $conditions = array_values($conditions);

        return in_array($condition, $conditions);
    }

    public function checkMetricCondition($condition)
    {
        if (empty($condition)) {
            throw new Exception(Piwik::translate('CustomAlerts_InvalidMetricCondition'));
        }

        if (!self::isValidMetricCondition($condition)) {
            throw new Exception(Piwik::translate('CustomAlerts_InvalidMetricCondition'));
        }
    }

    public function checkReportCondition($condition)
    {
        if (!empty($condition) && !self::isValidGroupCondition($condition)) {
            throw new Exception(Piwik::translate('CustomAlerts_InvalidReportCondition'));
        }
    }

    public function checkComparedTo($period, $comparedTo)
    {
        if (!self::isValidComparableDate($period, $comparedTo)) {
            throw new Exception(Piwik::translate('CustomAlerts_InvalidComparableDate'));
        }
    }

    /**
     * Checks whether a report + metric exists for
     * the given idSites and if the a dimension is
     * given (requires report_condition, report_matched)
     *
     * @param int $idSite
     * @param string $apiMethodUniqueId for example MultiSites.getAll
     * @param string $metric
     * @return void
     */
    public function checkApiMethodAndMetric($idSite, $apiMethodUniqueId, $metric)
    {
        Context::changeIdSite($idSite, function () use ($idSite, $apiMethodUniqueId, $metric) {
            if (empty($apiMethodUniqueId) || false === strpos($apiMethodUniqueId, '_')) {
                throw new Exception(Piwik::translate('CustomAlerts_InvalidReport'));
            }

            if (!$this->processedReport->isValidReportForSite($idSite, $apiMethodUniqueId)) {
                throw new Exception(Piwik::translate('CustomAlerts_InvalidReport'));
            }

            if (!$this->processedReport->isValidMetricForReport($metric, $idSite, $apiMethodUniqueId)) {
                throw new Exception(Piwik::translate('CustomAlerts_InvalidMetric'));
            }
        });
    }

    /**
     * @param $alert
     * @throws \Exception
     */
    public function checkUserHasPermissionForAlert($alert)
    {
        if (Piwik::getCurrentUserLogin() != $alert['login']) {
            throw new Exception(Piwik::translate('CustomAlerts_AccessException', $alert['idalert']));
        }
    }
}
