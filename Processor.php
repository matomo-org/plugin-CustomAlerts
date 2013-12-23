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

/**
 *
 * @package Piwik_CustomAlerts
 */
class Processor extends \Piwik\Plugin
{
    public static function getGroupConditions()
    {
        return array(
            'CustomAlerts_MatchesExactly'      => 'matches_exactly',
            'CustomAlerts_DoesNotMatchExactly' => 'does_not_match_exactly',
            'CustomAlerts_MatchesRegularExpression'      => 'matches_regex',
            'CustomAlerts_DoesNotMatchRegularExpression' => 'does_not_match_regex',
            'CustomAlerts_Contains'         => 'contains',
            'CustomAlerts_DoesNotContain'   => 'does_not_contain',
            'CustomAlerts_StartsWith'       => 'starts_with',
            'CustomAlerts_DoesNotStartWith' => 'does_not_start_with',
            'CustomAlerts_EndsWith'         => 'ends_with',
            'CustomAlerts_DoesNotEndWith'   => 'does_not_end_with',
        );
    }

    public static function getMetricConditions()
    {
        return array(
            'CustomAlerts_IsLessThan'    => 'less_than',
            'CustomAlerts_IsGreaterThan' => 'greater_than',
            'CustomAlerts_DecreasesMoreThan' => 'decrease_more_than',
            'CustomAlerts_IncreasesMoreThan' => 'increase_more_than',
            'CustomAlerts_PercentageDecreasesMoreThan' => 'percentage_decrease_more_than',
            'CustomAlerts_PercentageIncreasesMoreThan' => 'percentage_increase_more_than',
        );
    }

	public function processAlerts($period)
	{
        $model  = new Model();
		$alerts = $model->getAllAlerts($period);

		foreach ($alerts as $alert) {
			$this->processAlert($period, $alert);
		}
	}

    private function processAlert($period, $alert)
    {
        $metricOne = $this->getValueForAlertInPast($period, $alert, 1);

        // Do we have data? stop otherwise.
        if (is_null($metricOne)) {
            return;
        }

        $metricTwo = $this->getValueForAlertInPast($period, $alert, 2);

        if ($this->shouldBeTriggered($alert, $metricOne, $metricTwo)) {
            $this->triggerAlert($alert);
        }
    }

    protected function shouldBeTriggered($alert, $metricOne, $metricTwo)
    {
        if (!empty($metricTwo)) {
            $percentage = ((($metricOne / $metricTwo) * 100) - 100);
        } else {
            $percentage = $metricOne;
        }

        switch ($alert['metric_condition']) {
            case 'greater_than':
                return ($metricOne > floatval($alert['metric_matched']));
            case 'less_than':
                return ($metricOne < floatval($alert['metric_matched']));
            case 'decrease_more_than':
                return (($metricTwo - $metricOne) > $alert['metric_matched']);
            case 'increase_more_than':
                return (($metricOne - $metricTwo) > $alert['metric_matched']);
            case 'percentage_decrease_more_than':
                return ((-1 * $alert['metric_matched']) > $percentage && $percentage < 0);
            case 'percentage_increase_more_than':
                return ($alert['metric_matched'] < $percentage && $percentage >= 0);
        }

        throw new \Exception('Metric condition is not supported');
    }

	private function triggerAlert($alert)
	{
        $model = new Model();
        $model->triggerAlert($alert['idalert'], $alert['idsite']);
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
     */
    protected function filterDataTable($dataTable, $condition, $value)
    {
        $invert = false;

        // Some escaping?
        switch ($condition) {
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
     * @param  string $period
     * @param  array  $alert
     * @param  int    $subPeriodN
     *
     * @return array
     */
    private function getValueForAlertInPast($period, $alert, $subPeriodN)
    {
        $params = array(
            'method' => $alert['report'],
            'format' => 'original',
            'idSite' => $alert['idsite'],
            'period' => $period,
            'date'   => Date::today()->subPeriod($subPeriodN, $period)->toString()
        );

        // Get the data for the API request
        $request = new Piwik\API\Request($params);
        $table   = $request->process();

        // TODO are we always getting a dataTable?
        return $this->getMetricFromTable($table, $alert['metric'], $alert['report_condition'], $alert['report_matched']);
    }

}
?>
