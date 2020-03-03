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

use Piwik\API\Request as ApiRequest;
use Piwik\Common;
use Piwik\Context;
use Piwik\DataTable;
use Piwik\Date;
use Piwik\Plugins\API\ProcessedReport;
use Piwik\Site;

/**
 *
 */
class Processor
{
    public static function getComparablesDates()
    {
        return array(
            'day' => array(
                'CustomAlerts_DayComparedToPreviousWeek' => 7,
                'CustomAlerts_DayComparedToPreviousDay'  => 1,
                'CustomAlerts_DayComparedToPreviousYear' => 365,
            ),
            'week' => array(
                'CustomAlerts_WeekComparedToPreviousWeek' => 1,
            ),
            'month' => array(
                'CustomAlerts_MonthComparedToPreviousMonth' => 1,
                'CustomAlerts_MonthComparedToPreviousYear'  => 12,
            )
        );
    }

    public static function getGroupConditions()
    {
        return array(
            'CustomAlerts_MatchesAnyExpression' => 'matches_any',
            'CustomAlerts_OperationIs'      => 'matches_exactly',
            'CustomAlerts_OperationIsNot'   => 'does_not_match_exactly',
            'CustomAlerts_MatchesRegularExpression'      => 'matches_regex',
            'CustomAlerts_DoesNotMatchRegularExpression' => 'does_not_match_regex',
            'CustomAlerts_OperationContains'         => 'contains',
            'CustomAlerts_OperationDoesNotContain'   => 'does_not_contain',
            'CustomAlerts_StartsWith'       => 'starts_with',
            'CustomAlerts_DoesNotStartWith' => 'does_not_start_with',
            'CustomAlerts_EndsWith'         => 'ends_with',
            'CustomAlerts_DoesNotEndWith'   => 'does_not_end_with',
        );
    }

    public static function getMetricConditions()
    {
        return array(
            'CustomAlerts_IsLessThan'     => 'less_than',
            'CustomAlerts_IsGreaterThan'  => 'greater_than',
            'CustomAlerts_DecreasesMoreThan' => 'decrease_more_than',
            'CustomAlerts_IncreasesMoreThan' => 'increase_more_than',
            'CustomAlerts_PercentageDecreasesMoreThan' => 'percentage_decrease_more_than',
            'CustomAlerts_PercentageIncreasesMoreThan' => 'percentage_increase_more_than',
        );
    }

    /**
     * @var ProcessedReport
     */
    private $processedReport;

    /**
     * @var Validator
     */
    private $validator;

    public function __construct(ProcessedReport $processedReport, Validator $validator)
    {
        $this->processedReport = $processedReport;
        $this->validator = $validator;
    }

    public function processAlerts($period, $idSite)
    {
        $alerts = $this->getAllAlerts($period);

        foreach ($alerts as $alert) {
            $this->processAlert($alert, $idSite);
        }
    }

    protected function processAlert($alert, $idSite)
    {
        if (!$this->shouldBeProcessed($alert, $idSite)) {
            return;
        }

        $valueNew = $this->getValueForAlertInPast($alert, $idSite, 1);

        if (is_null($valueNew)) {
            $valueNew = 0;
        }

        if (365 == $alert['compared_to'] && Date::today()->isLeapYear()) {
            $alert['compared_to'] = 366;
        }

        $valueOld = $this->getValueForAlertInPast($alert, $idSite, 1 + $alert['compared_to']);

        if ($this->shouldBeTriggered($alert, $valueNew, $valueOld)) {
            $this->triggerAlert($alert, $idSite, $valueNew, $valueOld);
        }
    }

    private function shouldBeProcessed($alert, $idSite)
    {
        if (empty($alert['id_sites']) || !in_array($idSite, $alert['id_sites'])) {
            return false;
        }

        if (!$this->validator->isValidComparableDate($alert['period'], $alert['compared_to'])) {
            // actually it would be nice to log or send a notification or whatever that we have skipped an alert
            return false;
        }

        if (!$this->reportExists($idSite, $alert['report'], $alert['metric'])) {
            // actually it would be nice to log or send a notification or whatever that we have skipped an alert
            return false;
        }

        return true;
    }

    private function reportExists($idSite, $report, $metric)
    {
        try {
            $this->validator->checkApiMethodAndMetric($idSite, $report, $metric);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    private function needsBothValuesToTrigger($alert)
    {
        $comparisons = array(
            'decrease_more_than',
            'increase_more_than',
            'percentage_decrease_more_than',
            'percentage_increase_more_than'
        );

        return in_array($alert['metric_condition'], $comparisons);
    }

    protected function shouldBeTriggered($alert, $valueNew, $valueOld)
    {
        if ($this->needsBothValuesToTrigger($alert) && empty($valueOld) && empty($valueNew)) {
            return false;
        }

        if (!empty($valueOld)) {
            $percentage = ((($valueNew / $valueOld) * 100) - 100);
        } else {
            $percentage = $valueNew;
        }

        $metricMatched = floatval($alert['metric_matched']);

        switch ($alert['metric_condition']) {
            case 'greater_than':
                return ($valueNew > $metricMatched);
            case 'less_than':
                return ($valueNew < $metricMatched);
            case 'decrease_more_than':
                return (($valueOld - $valueNew) > $metricMatched);
            case 'increase_more_than':
                return (($valueNew - $valueOld) > $metricMatched);
            case 'percentage_decrease_more_than':
                return ((-1 * $metricMatched) > $percentage && $percentage < 0);
            case 'percentage_increase_more_than':
                return ($metricMatched < $percentage && $percentage >= 0);
        }

        throw new \Exception('Metric condition is not supported');
    }

    /**
     * @param DataTable $dataTable DataTable
     * @param string $metric       Metric to fetch from row.
     * @param string $filterCond   Condition to filter for.
     * @param string $filterValue  Value to find
     *
     * @return mixed
     */
    protected function aggregateToOneValue($dataTable, $metric, $filterCond = '', $filterValue = '')
    {
        if (!empty($filterValue)) {
            $this->filterDataTable($dataTable, $filterCond, $filterValue);
        }

        if ($dataTable->getRowsCount() > 1) {
            $dataTable->filter('Truncate', array(0, null, $metric));
        }

        $dataTable->applyQueuedFilters();

        $dataRow = $dataTable->getFirstRow();

        if (!$dataRow) {
            return null;
        }

        $value = $dataRow->getColumn($metric);

        if ($value && is_string($value)) {
            $value = str_replace(array('%', 's'), '', $value);
        }

        return $value;
    }

    /**
     * @param $dataTable
     * @param $condition
     * @param $value
     * @throws \Exception
     */
    protected function filterDataTable($dataTable, $condition, $value)
    {
        $invert = false;

        $value  = Common::unsanitizeInputValue($value);
        if ('matches_regex' != $condition && 'does_not_match_regex' != $condition) {
            $value = str_replace(array('?', '+', '*'), array('\?', '\+', '\*'), $value);
        }

        // Some escaping?
        switch ($condition) {
            case 'matches_any':
                return;
            case 'matches_exactly':
                $pattern = sprintf("^%s$", $value);
                break;
            case 'matches_regex':
                $pattern = $value;
                break;
            case 'does_not_match_exactly':
                $pattern = sprintf("^%s$", $value);
                $invert  = true;
                break;
            case 'does_not_match_regex':
                $pattern = sprintf("%s", $value);
                $invert  = true;
                break;
            case 'contains':
                $pattern = $value;
                break;
            case 'does_not_contain':
                $pattern = $value;
                $invert  = true;
                break;
            case 'starts_with':
                $pattern = sprintf("^%s", $value);
                break;
            case 'does_not_start_with':
                $pattern = sprintf("^%s", $value);
                $invert  = true;
                break;
            case 'ends_with':
                $pattern = sprintf("%s$", $value);
                break;
            case 'does_not_end_with':
                $pattern = sprintf("%s$", $value);
                $invert  = true;
                break;
            default:
                throw new \Exception('Filter condition not supported');
        }

        $dataTable->filter('Pattern', array('label', $pattern, $invert));
    }

    private function getDateForAlertInPast($idSite, $period, $subPeriodN)
    {
        $timezone = Site::getTimezoneFor($idSite);
        $date     = Date::now();
        $date     = Date::factory($date->getDatetime(), $timezone);

        if ($subPeriodN) {
            $date = $date->subPeriod($subPeriodN, $period);
        }

        return $date->toString();
    }

    /**
     * @param  array  $alert
     * @param  int    $idSite
     * @param  int    $subPeriodN
     *
     * @return array
     */
    public function getValueForAlertInPast($alert, $idSite, $subPeriodN)
    {
        $report = Context::changeIdSite($idSite, function () use ($idSite, $alert) {
            return $this->processedReport->getReportMetadataByUniqueId($idSite, $alert['report']);
        });

        if (empty($report)) {
            throw new \Exception("Could not find report for alert '{$alert['report']}'.");
        }

        $dateInPast = $this->getDateForAlertInPast($idSite, $alert['period'], $subPeriodN);

        $params = array(
            'format' => 'original',
            'idSite' => $idSite,
            'period' => $alert['period'],
            'date'   => $dateInPast,
            'flat'   => 1,
            'disable_queued_filters' => 1,
            'filter_limit' => -1
        );

        if (!empty($report['parameters'])) {
            $params = array_merge($params, $report['parameters']);
        }

        $subtableId = DataTable\Manager::getInstance()->getMostRecentTableId();

        $table = ApiRequest::processRequest($report['module'] . '.' . $report['action'], $params, $default = []);

        $value   = $this->aggregateToOneValue($table, $alert['metric'], $alert['report_condition'], $alert['report_matched']);

        DataTable\Manager::getInstance()->deleteAll($subtableId);

        return $value;
    }

    protected function triggerAlert($alert, $idSite, $valueNew, $valueOld)
    {
        $this->getModel()->triggerAlert($alert['idalert'], $idSite, $valueNew, $valueOld, Date::now()->getDatetime());
    }

    private function getAllAlerts($period)
    {
        return $this->getModel()->getAllAlertsForPeriod($period);
    }

    private function getModel()
    {
        return new Model();
    }

}
