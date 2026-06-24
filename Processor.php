<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 */

namespace Piwik\Plugins\CustomAlerts;

use Piwik\API\Request as ApiRequest;
use Piwik\Common;
use Piwik\Container\StaticContainer;
use Piwik\Context;
use Piwik\DataTable;
use Piwik\Date;
use Piwik\Piwik;
use Piwik\Plugins\API\ProcessedReport;
use Piwik\Scheduler\RetryableException;
use Piwik\Plugins\SitesManager\API as SitesManagerApi;
use Piwik\Site;

/**
 *
 */
class Processor
{
    /**
     * @var ProcessedReport
     */
    private $processedReport;
    /**
     * @var Validator
     */
    private $validator;
    /**
     * @var Model
     */
    private $model;

    public function __construct(ProcessedReport $processedReport, Validator $validator, Model $model)
    {
        $this->processedReport = $processedReport;
        $this->validator       = $validator;
        $this->model           = $model;
    }

    public static function getComparablesDates()
    {
        return array(
            'day'   => array(
                'CustomAlerts_DayComparedToPreviousWeek' => 7,
                'CustomAlerts_DayComparedToPreviousDay'  => 1,
                'CustomAlerts_DayComparedToPreviousYear' => 365,
            ),
            'week'  => array(
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
            'CustomAlerts_MatchesAnyExpression'          => 'matches_any',
            'CustomAlerts_OperationIs'                   => 'matches_exactly',
            'CustomAlerts_OperationIsNot'                => 'does_not_match_exactly',
            'CustomAlerts_MatchesRegularExpression'      => 'matches_regex',
            'CustomAlerts_DoesNotMatchRegularExpression' => 'does_not_match_regex',
            'CustomAlerts_OperationContains'             => 'contains',
            'CustomAlerts_OperationDoesNotContain'       => 'does_not_contain',
            'CustomAlerts_StartsWith'                    => 'starts_with',
            'CustomAlerts_DoesNotStartWith'              => 'does_not_start_with',
            'CustomAlerts_EndsWith'                      => 'ends_with',
            'CustomAlerts_DoesNotEndWith'                => 'does_not_end_with',
        );
    }

    public static function getMetricConditions()
    {
        return array(
            'CustomAlerts_IsLessThan'                  => 'less_than',
            'CustomAlerts_IsGreaterThan'               => 'greater_than',
            'CustomAlerts_DecreasesMoreThan'           => 'decrease_more_than',
            'CustomAlerts_IncreasesMoreThan'           => 'increase_more_than',
            'CustomAlerts_PercentageDecreasesMoreThan' => 'percentage_decrease_more_than',
            'CustomAlerts_PercentageIncreasesMoreThan' => 'percentage_increase_more_than',
        );
    }

    /**
     * @param $period
     * @param $idSite
     * @return void
     * @throws RetryableException The exception that should be caught and handled by the Scheduler class. Only happens
     * if there's an alert that can't be processed yet and needs to be retried.
     */
    public function processAlerts($period, $idSite)
    {
        $alerts = $this->getAllAlerts($period);

        $previouslyProcessedAlerts = $this->getPreviouslyProcessedAlerts($period, intval($idSite));

        $failedAlerts = [];
        foreach ($alerts as $alert) {
            // Skip alerts that were processed previously
            if (in_array($alert['idalert'], array_column($previouslyProcessedAlerts, 'idalert'))) {
                continue;
            }

            try {
                $this->processAlert($alert, $idSite);
            } catch (RetryableException $e) {
                $failedAlerts[] = $alert;
            }
        }

        if (!empty($failedAlerts)) {
            // Build up a retry exception message based of the alerts that failed to process
            $alertsString = '';
            foreach ($failedAlerts as $failedAlert) {
                $alertsString .= "\nID: {$failedAlert['idalert']} Name: {$failedAlert['name']} Login: {$failedAlert['login']} Report: {$failedAlert['report']}";
            }

            if (CustomAlerts::$currentlyRunningScheduledTaskRetryCount === 3) {
                StaticContainer::get(\Piwik\Log\LoggerInterface::class)->warning(Piwik::translate('CustomAlerts_FinalTaskRetryWarning', [$alertsString]));
            }

            // Throw an exception to let the scheduler know to retry the task
            throw new RetryableException(Piwik::translate('CustomAlerts_TaskRetryExceptionMessage', [$alertsString]));
        }
    }

    protected function getPreviouslyProcessedAlerts(string $period, int $idSite): array
    {
        return $this->model->getTriggeredAlertsFromPastNHours($period, $idSite, 12);
    }

    private function getAllAlerts($period)
    {
        return $this->getModel()->getAllAlertsForPeriod($period);
    }

    private function getModel()
    {
        return new Model();
    }

    /**
     * @param $alert
     * @param $idSite
     * @return void
     * @throws RetryableException
     */
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

        if (!$this->hasViewPermissionForAlertOwner($alert, $idSite)) {
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

    protected function hasViewPermissionForAlertOwner(array $alert, int $idSite): bool
    {
        if (empty($alert['login'])) {
            return false;
        }

        $idSitesUserHasAccess = SitesManagerApi::getInstance()->getSitesIdWithAtLeastViewAccess($alert['login']);

        return !empty($idSitesUserHasAccess) && in_array($idSite, $idSitesUserHasAccess);
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

    /**
     * @param array $alert
     * @param int $idSite
     * @param int $subPeriodN
     *
     * @return array
     * @throws RetryableException If the report has an archive status, and it's something other than complete
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
            'format'                 => 'original',
            'idSite'                 => $idSite,
            'period'                 => $alert['period'],
            'date'                   => $dateInPast,
            'flat'                   => 1,
            'disable_queued_filters' => 1,
            'filter_limit'           => -1
        );

        // Only include the archive state param for versions of Matomo that allow it
        if (version_compare(\Piwik\Version::VERSION, '5.1.0-b1', '>=')) {
            $params['fetch_archive_state'] = 1;
        }

        if (!empty($report['parameters'])) {
            $params = array_merge($params, $report['parameters']);
        }

        $params = $this->restrictMultiSitesReportToAlertOwner($params, $report, $alert);

        $subtableId = DataTable\Manager::getInstance()->getMostRecentTableId();

        $table = ApiRequest::processRequest($report['module'] . '.' . $report['action'], $params, $default = []);

        // If the response is a DataTable, check the archiving status
        if ($table instanceof DataTable) {
            $this->checkWhetherArchiveIsComplete($alert, $table);
        }

        $value = $this->aggregateToOneValue($table, $alert['metric'], $alert['report_condition'], $alert['report_matched']);

        DataTable\Manager::getInstance()->deleteAll($subtableId);

        return $value;
    }

    protected function restrictMultiSitesReportToAlertOwner(array $params, array $report, array $alert): array
    {
        if (
            $report['module'] !== 'MultiSites'
            || $report['action'] !== 'getAll'
            || empty($alert['login'])
            || Piwik::hasTheUserSuperUserAccess($alert['login'])
        ) {
            return $params;
        }

        $params['_restrictSitesToLogin'] = $alert['login'];

        return $params;
    }

    /**
     * Checks whether the archive status is complete. We throw an exception if the status is something other than
     * complete. If no status is found, we do nothing.
     *
     * @param array $alert Array containing all the alert information
     * @param DataTable $table Should have the archive_state metadata set because the fetch_archive_state query param
     * was set as part of the API request.
     *
     * @return void
     * @throws RetryableException If the archive status is found and isn't complete
     */
    protected function checkWhetherArchiveIsComplete(array $alert, DataTable $table): void
    {
        // Don't bother checking older versions of Matomo since the data and constants won't be there
        if (version_compare(\Piwik\Version::VERSION, '5.1.0-b1', '<')) {
            return;
        }

        $archiveState = $table->getMetadata(DataTable::ARCHIVE_STATE_METADATA_NAME);
        if (empty($archiveState)) {
            return;
        }

        if ($archiveState === \Piwik\Archive\ArchiveState::COMPLETE) {
            return;
        }

        // Throw an exception since the archive status was provided and isn't complete
        throw new RetryableException('This alert is not ready to process due to incomplete archiving');
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
     * @param DataTable $dataTable   DataTable
     * @param string    $metric      Metric to fetch from row.
     * @param string    $filterCond  Condition to filter for.
     * @param string    $filterValue Value to find
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

        $value = Common::unsanitizeInputValue($value);
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

    protected function triggerAlert($alert, $idSite, $valueNew, $valueOld)
    {
        $this->getModel()->triggerAlert($alert['idalert'], $idSite, $valueNew, $valueOld, Date::now()->getDatetime());
    }
}
