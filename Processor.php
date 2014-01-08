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
                'CustomAlerts_ComparedToPreviousDay'  => 1,
                'CustomAlerts_ComparedToPreviousWeek' => 7,
                'CustomAlerts_ComparedToPreviousYear' => 365,
            ),
            'week' => array(
                'CustomAlerts_ComparedToPreviousWeek' => 1,
            ),
            'month' => array(
                'CustomAlerts_ComparedToPreviousMonth' => 1,
                'CustomAlerts_ComparedToPreviousYear'  => 12,
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
            'General_OperationLessThan'     => 'less_than',
            'General_OperationGreaterThan'  => 'greater_than',
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

	public function processAlerts($period)
	{
        $alerts = $this->getAllAlerts($period);

		foreach ($alerts as $alert) {
			$this->processAlert($alert);
		}
	}

    protected function processAlert($alert)
    {
        if (empty($alert['idSites'])) {
            return;
        }

        foreach ($alert['idSites'] as $idSite) {

            if (!$this->reportExists($idSite, $alert['report'], $alert['metric'])) {
                // actually it would be nice to log or send a notification or whatever that we have skipped an alert
                continue;
            }

            $valueNew = $this->getValueForAlertInPast($alert, $idSite, 1);

            // Do we have data? stop otherwise.
            if (is_null($valueNew)) {
                continue;
            }

            $valueOld = $this->getValueForAlertInPast($alert, $idSite, 2);

            if ($this->shouldBeTriggered($alert, $valueNew, $valueOld)) {
                $this->triggerAlert($alert, $idSite, $valueNew, $valueOld);
            }
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

        switch ($alert['metric_condition']) {
            case 'greater_than':
                return ($valueNew > floatval($alert['metric_matched']));
            case 'less_than':
                return ($valueNew < floatval($alert['metric_matched']));
            case 'decrease_more_than':
                return (($valueOld - $valueNew) > $alert['metric_matched']);
            case 'increase_more_than':
                return (($valueNew - $valueOld) > $alert['metric_matched']);
            case 'percentage_decrease_more_than':
                return ((-1 * $alert['metric_matched']) > $percentage && $percentage < 0);
            case 'percentage_increase_more_than':
                return ($alert['metric_matched'] < $percentage && $percentage >= 0);
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

		$dataRow = $dataTable->getFirstRow();

		if ($dataRow) {
			return $dataRow->getColumn($metric);
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
            'filter_truncate' => 0
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