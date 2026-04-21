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
 * Exposes Custom Alerts API endpoints for managing alert definitions and reading triggered alert data.
 * These methods let callers create, update, fetch, delete, and evaluate alerts for one or more sites.
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
     * Returns the computed alert value for each site linked to an alert for a past or current period.
     * Use 0 to evaluate the current period, 1 for the previous matching period, and so on.
     *
     * @param int $idAlert Alert ID to evaluate.
     * @param int|string $subPeriodN Number of periods in the past to evaluate.
     *                               Use 0 for the current day, week, or month.
     *
     * @return list<array{idSite: int, value: mixed}> Alert values grouped by site.
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
     * Returns a single custom alert definition.
     *
     * @param int $idAlert Alert ID to fetch.
     *
     * @return array{id_sites: list<int>} & array<string, mixed> Alert definition including the configured site IDs.
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
     * Returns the custom alerts configured for the requested sites.
     *
     * @param string|array $idSites Website ID(s) to query.
     *                              Accepts comma-separated IDs, "all", numeric IDs as strings, or ["all"].
     * @param bool $ifSuperUserReturnAllAlerts Whether a super user should receive alerts created by all users.
     *
     * @return list<array<string, mixed>> Alert definitions accessible to the current user for the requested sites.
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
     * Creates a custom alert for one or more sites.
     *
     * @param string $name Alert name.
     * @param string|array $idSites Website ID(s) to query.
     *                              Accepts comma-separated IDs, "all", numeric IDs as strings, or ["all"].
     * @param 'day'|'week'|'month' $period Alert period.
     *                                     Allowed values: day, week, month.
     * @param bool $emailMe Whether to notify the current user by email when the alert triggers.
     * @param list<string> $additionalEmails Additional email recipients for email notifications.
     * @param list<string> $phoneNumbers Mobile Messaging recipients when the mobile channel is enabled.
     * @param string $metric Metric unique ID to evaluate, for example nb_uniq_visits or sum_visit_length.
     * @param string $metricCondition Comparison rule to apply to the metric.
     *                                Allowed values: less_than, greater_than, decrease_more_than,
     *                                increase_more_than, percentage_decrease_more_than,
     *                                percentage_increase_more_than.
     * @param float|int|string $metricValue Threshold value to compare the selected metric against.
     * @param int $comparedTo Number of prior periods to compare against.
     *                        Allowed values by period: day => 1, 7, 365; week => 1; month => 1, 12.
     * @param string $reportUniqueId Report unique ID in module_action format.
     * @param false|string $reportCondition Optional dimension filter condition for report rows.
     *                                      Allowed values: matches_any, matches_exactly, does_not_match_exactly,
     *                                      matches_regex, does_not_match_regex, contains, does_not_contain,
     *                                      starts_with, does_not_start_with, ends_with, does_not_end_with.
     * @param false|string $reportValue Value to match when $reportCondition is provided.
     * @param list<'email'|'mobile'|'slack'|'teams'> $reportMediums Delivery channels to use for notifications.
     *                                                Allowed values: email, mobile, slack, teams.
     * @param string $slackChannelID Slack channel ID when the slack channel is enabled.
     * @param string $msTeamsWebhookUrl Microsoft Teams webhook URL when the teams channel is enabled.
     * @return int ID of the newly created alert.
     */
    public function addAlert($name, $idSites, $period, $emailMe, $additionalEmails, $phoneNumbers, $metric, $metricCondition, $metricValue, $comparedTo, $reportUniqueId, $reportCondition = false, $reportValue = false, array $reportMediums = [], string $slackChannelID = '', string $msTeamsWebhookUrl = '')
    {
        $idSites          = Site::getIdSitesFromIdSitesString($idSites);

        $this->checkAlert($idSites, $name, $period, $emailMe, $additionalEmails, $phoneNumbers, $slackChannelID, $msTeamsWebhookUrl, $metricCondition, $metric, $comparedTo, $reportCondition, $reportUniqueId, $reportMediums);

        $name  = Common::unsanitizeInputValue($name);
        $login = Piwik::getCurrentUserLogin();

        if (empty($reportCondition) || empty($reportValue)) {
            $reportCondition = null;
            $reportValue     = null;
        }

        $metricValue = Common::forceDotAsSeparatorForDecimalPoint((float)$metricValue);

        return $this->getModel()->createAlert($name, $idSites, $login, $period, $emailMe, $additionalEmails, $phoneNumbers, $metric, $metricCondition, $metricValue, $comparedTo, $reportUniqueId, $reportCondition, $reportValue, $reportMediums, $slackChannelID, $msTeamsWebhookUrl);
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

    private function checkAlert($idSites, $name, $period, &$emailMe, &$additionalEmails, &$phoneNumbers, &$slackChannelID, &$msTeamsWebhookUrl, $metricCondition, $metricValue, $comparedTo, $reportCondition, $reportUniqueId, $reportMediums)
    {
        Piwik::checkUserHasViewAccess($idSites);
        $additionalEmails = in_array('email', $reportMediums) ? $this->filterAdditionalEmails($additionalEmails) : [];
        $phoneNumbers = in_array('mobile', $reportMediums) ? $this->filterPhoneNumbers($phoneNumbers) : [];
        $emailMe = in_array('email', $reportMediums) && $emailMe;
        $slackChannelID = in_array('slack', $reportMediums) ? $slackChannelID : '';
        $msTeamsWebhookUrl = in_array('teams', $reportMediums) ? $msTeamsWebhookUrl : '';

        $this->validator->checkName($name);
        $this->validator->checkPeriod($period);
        $this->validator->checkComparedTo($period, $comparedTo);
        $this->validator->checkMetricCondition($metricCondition);
        $this->validator->checkReportCondition($reportCondition);
        $this->validator->checkReportMediums($reportMediums);

        foreach ($idSites as $idSite) {
            $this->validator->checkApiMethodAndMetric($idSite, $reportUniqueId, $metricValue);
        }

        $this->validator->checkAdditionalEmails($additionalEmails);

        foreach ($reportMediums as $reportMedium) {
            Piwik::postEvent('CustomAlerts.validateReportParameters', [get_defined_vars(), $reportMedium]);
        }
    }

    /**
     * Updates an existing custom alert.
     *
     * @param int $idAlert Alert ID to update.
     * @param string $name Alert name.
     * @param string|array $idSites Website ID(s) to query.
     *                              Accepts comma-separated IDs, "all", numeric IDs as strings, or ["all"].
     * @param 'day'|'week'|'month' $period Alert period.
     *                                     Allowed values: day, week, month.
     * @param bool $emailMe Whether to notify the current user by email when the alert triggers.
     * @param list<string> $additionalEmails Additional email recipients for email notifications.
     * @param list<string> $phoneNumbers Mobile Messaging recipients when the mobile channel is enabled.
     * @param string $metric Metric unique ID to evaluate, for example nb_uniq_visits or sum_visit_length.
     * @param string $metricCondition Comparison rule to apply to the metric.
     *                                Allowed values: less_than, greater_than, decrease_more_than,
     *                                increase_more_than, percentage_decrease_more_than,
     *                                percentage_increase_more_than.
     * @param float|int|string $metricValue Threshold value to compare the selected metric against.
     * @param int $comparedTo Number of prior periods to compare against.
     *                        Allowed values by period: day => 1, 7, 365; week => 1; month => 1, 12.
     * @param string $reportUniqueId Report unique ID in module_action format.
     * @param false|string $reportCondition Optional dimension filter condition for report rows.
     *                                      Allowed values: matches_any, matches_exactly, does_not_match_exactly,
     *                                      matches_regex, does_not_match_regex, contains, does_not_contain,
     *                                      starts_with, does_not_start_with, ends_with, does_not_end_with.
     * @param false|string $reportValue Value to match when $reportCondition is provided.
     * @param list<'email'|'mobile'|'slack'|'teams'> $reportMediums Delivery channels to use for notifications.
     *                                                Allowed values: email, mobile, slack, teams.
     * @param string $slackChannelID Slack channel ID when the slack channel is enabled.
     * @param string $msTeamsWebhookUrl Microsoft Teams webhook URL when the teams channel is enabled.
     *
     * @return int Updated alert ID.
     */
    public function editAlert($idAlert, $name, $idSites, $period, $emailMe, $additionalEmails, $phoneNumbers, $metric, $metricCondition, $metricValue, $comparedTo, $reportUniqueId, $reportCondition = false, $reportValue = false, array $reportMediums = [], string $slackChannelID = '', string $msTeamsWebhookUrl = '')
    {
        // make sure alert exists and user has permission to read
        $this->getAlert($idAlert);

        $idSites          = Site::getIdSitesFromIdSitesString($idSites);

        $this->checkAlert($idSites, $name, $period, $emailMe, $additionalEmails, $phoneNumbers, $slackChannelID, $msTeamsWebhookUrl, $metricCondition, $metric, $comparedTo, $reportCondition, $reportUniqueId, $reportMediums);

        $name = Common::unsanitizeInputValue($name);

        if (empty($reportCondition) || empty($reportValue)) {
            $reportCondition = null;
            $reportValue     = null;
        }

        $metricValue = Common::forceDotAsSeparatorForDecimalPoint((float)$metricValue);

        return $this->getModel()->updateAlert($idAlert, $name, $idSites, $period, $emailMe, $additionalEmails, $phoneNumbers, $metric, $metricCondition, $metricValue, $comparedTo, $reportUniqueId, $reportCondition, $reportValue, $reportMediums, $slackChannelID, $msTeamsWebhookUrl);
    }

    /**
     * Deletes an existing custom alert.
     *
     * @param int $idAlert Alert ID to delete.
     *
     * @return void
     */
    public function deleteAlert($idAlert)
    {
        // make sure alert exists and user has permission to read
        $this->getAlert($idAlert);

        $this->getModel()->deleteAlert($idAlert);
    }

    /**
     * Returns triggered alerts for the current user and requested sites.
     *
     * @param string|array $idSites Website ID(s) to query.
     *                              Accepts comma-separated IDs, "all", numeric IDs as strings, or ["all"].
     *
     * @return list<array<string, mixed>> Triggered alert entries for the current user and requested sites.
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
