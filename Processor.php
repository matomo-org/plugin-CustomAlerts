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

use Piwik;
use Piwik\DataTable;
use Piwik\Date;
use Piwik\Db;
use Piwik\Plugins\API\ProcessedReport;

/**
 *
 * @package Piwik_CustomAlerts
 */
class Processor extends \Piwik\Plugin
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
            'General_OperationIs'      => 'matches_exactly',
            'General_OperationIsNot'   => 'does_not_match_exactly',
            'CustomAlerts_MatchesRegularExpression'      => 'matches_regex',
            'CustomAlerts_DoesNotMatchRegularExpression' => 'does_not_match_regex',
            'General_OperationContains'         => 'contains',
            'General_OperationDoesNotContain'   => 'does_not_contain',
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

    public static function isValidComparableDate($period, $comparedToDate)
    {
        $dates = self::getComparablesDates();
        if (!array_key_exists($period, $dates)) {
            return false;
        }

        return in_array($comparedToDate, array_values($dates[$period]));
    }

    public static function isValidGroupCondition($condition)
    {
        $conditions = self::getGroupConditions();
        $conditions = array_values($conditions);

        return in_array($condition, $conditions);
    }

    public static function isValidMetricCondition($condition)
    {
        $conditions = self::getMetricConditions();
        $conditions = array_values($conditions);

        return in_array($condition, $conditions);
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
        if (empty($alert['id_sites']) || !in_array($idSite, $alert['id_sites'])) {
            return;
        }

        if (!self::isValidComparableDate($alert['period'], $alert['compared_to'])) {
            // actually it would be nice to log or send a notification or whatever that we have skipped an alert
            return;
        }

        if (!$this->reportExists($idSite, $alert['report'], $alert['metric'])) {
            // actually it would be nice to log or send a notification or whatever that we have skipped an alert
            return;
        }

        $valueNew = $this->getValueForAlertInPast($alert, $idSite, 1);

        // Do we have data? stop otherwise.
        if (is_null($valueNew)) {
            return;
        }

        if (365 == $alert['compared_to'] && Date::today()->isLeapYear()) {
            $alert['compared_to'] = 366;
        }

        $valueOld = $this->getValueForAlertInPast($alert, $idSite, 1 + $alert['compared_to']);

        if ($this->shouldBeTriggered($alert, $valueNew, $valueOld)) {
            $this->triggerAlert($alert, $idSite, $valueNew, $valueOld);
        }
    }

    private function reportExists($idSite, $report, $metric)
    {
        $processedReport = new ProcessedReport();

        list($module, $action) = explode('.' , $report);

        return $processedReport->isValidMetricForReport($metric, $idSite, $module, $action);
    }

    protected function shouldBeTriggered($alert, $valueNew, $valueOld)
    {
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
     * @param string $metric Metric to fetch from row.
     * @param string $filterCond Condition to filter for.
     * @param string $filterValue Value to find
     *
     * @return mixed
     */
	protected function getMetricFromTable($dataTable, $metric, $filterCond = '', $filterValue = '')
	{

		if (!empty($filterValue)) {
            $this->filterDataTable($dataTable, $filterCond, $filterValue);
		}

		if ($dataTable->getRowsCount() > 1) {
			$dataTable->filter('Truncate', array(0, null, $metric));
		}

        $dataTable->applyQueuedFilters();

		$dataRow = $dataTable->getFirstRow();

		if ($dataRow) {
			$value = $dataRow->getColumn($metric);

            if ($value) {
                $value = str_replace(array('%', 's'), '', $value);
            }

            return $value;
        }

        return null;
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

    /**
     * @param  array  $alert
     * @param  int    $idSite
     * @param  int    $subPeriodN
     *
     * @return array
     */
    protected function getValueForAlertInPast($alert, $idSite, $subPeriodN)
    {
        $params = array(
            'method' => $alert['report'],
            'format' => 'original',
            'idSite' => $idSite,
            'period' => $alert['period'],
            'date'   => Date::today()->subPeriod($subPeriodN, $alert['period'])->toString(),
            'flat'   => 1,
            'disable_queued_filters' => 1
        );

        // Get the data for the API request
        $request = new Piwik\API\Request($params);
        $table   = $request->process();

        return $this->getMetricFromTable($table, $alert['metric'], $alert['report_condition'], $alert['report_matched']);
    }

    protected function triggerAlert($alert, $idSite, $valueNew, $valueOld)
    {
        $api = API::getInstance();
        $api->triggerAlert($alert['idalert'], $idSite, $valueNew, $valueOld);
    }

    private function getAllAlerts($period)
    {
        $api = API::getInstance();
        return $api->getAllAlertsForPeriod($period);
    }

}