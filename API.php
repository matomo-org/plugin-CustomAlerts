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
 * Provides API methods to create, update, fetch, and delete custom alerts.
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
     * @param int $idAlert Alert ID to evaluate.
     * @param int $subPeriodN Number of periods in the past to evaluate. Use 0 for the current period.
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
     * Returns a single alert.
     *
     * @param int $idAlert Alert ID to fetch.
     *
     * @return array{id_sites: list<int>} & array<string, mixed> Alert definition.
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
     * @param string|array $idSites Website ID(s) to query.
     *                              Accepts comma-separated IDs, "all", numeric IDs as strings, or ["all"].
     * @param bool $ifSuperUserReturnAllAlerts Whether to return all users' alerts when the current user is super user.
     *
     * @return list<array<string, mixed>> Alerts accessible to the current user.
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
     * @param string $name Alert name.
     * @param string|array $idSites Website ID(s) to query.
     *                              Accepts comma-separated IDs, "all", numeric IDs as strings, or ["all"].
     * @param 'day'|'week'|'month' $period Alert period.
     *                                     Allowed values: day, week, month.
     * @param bool $emailMe Whether to send email notifications to the current user.
     * @param list<string> $additionalEmails Additional email recipients.
     * @param list<string> $phoneNumbers Mobile Messaging recipients.
     * @param string $metric Metric unique ID (for example nb_uniq_visits, sum_visit_length).
     * @param string $metricCondition Metric comparison condition.
     *                                Allowed values: less_than, greater_than, decrease_more_than,
     *                                increase_more_than, percentage_decrease_more_than,
     *                                percentage_increase_more_than.
     * @param float|int|string $metricValue Metric to check for the selected report.
     * @param int $comparedTo Number of prior periods to compare against.
     *                        Allowed values by period: day => 1, 7, 365; week => 1; month => 1, 12.
     * @param string $reportUniqueId Report unique ID in format module_action.
     * @param false|string $reportCondition Group condition to apply.
     *                                      Allowed values: matches_any, matches_exactly, does_not_match_exactly,
     *                                      matches_regex, does_not_match_regex, contains, does_not_contain,
     *                                      starts_with, does_not_start_with, ends_with, does_not_end_with.
     * @param false|string $reportValue Value used by $reportCondition.
     * @param list<'email'|'mobile'|'slack'|'teams'> $reportMediums Delivery channels.
     *                                                Allowed values: email, mobile, slack, teams.
     * @param string $slackChannelID Slack channel ID when "slack" medium is enabled.
     * @param string $msTeamsWebhookUrl Microsoft Teams webhook URL when "teams" medium is enabled.
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
     * Edits an Alert for given website(s).
     *
     * @param int $idAlert Alert ID to update.
     * @param string $name Name of alert.
     * @param string|array $idSites Website ID(s) to query.
     *                              Accepts comma-separated IDs, "all", numeric IDs as strings, or ["all"].
     * @param 'day'|'week'|'month' $period Alert period.
     *                                     Allowed values: day, week, month.
     * @param bool $emailMe Whether to send email notifications to the current user.
     * @param list<string> $additionalEmails Additional email recipients.
     * @param list<string> $phoneNumbers Mobile Messaging recipients.
     * @param string $metric Metric unique ID (for example nb_uniq_visits, sum_visit_length).
     * @param string $metricCondition Metric comparison condition.
     *                                Allowed values: less_than, greater_than, decrease_more_than,
     *                                increase_more_than, percentage_decrease_more_than,
     *                                percentage_increase_more_than.
     * @param float|int|string $metricValue Metric to check for the selected report.
     * @param int $comparedTo Number of prior periods to compare against.
     *                        Allowed values by period: day => 1, 7, 365; week => 1; month => 1, 12.
     * @param string $reportUniqueId Report unique ID in format module_action.
     * @param false|string $reportCondition Group condition to apply.
     *                                      Allowed values: matches_any, matches_exactly, does_not_match_exactly,
     *                                      matches_regex, does_not_match_regex, contains, does_not_contain,
     *                                      starts_with, does_not_start_with, ends_with, does_not_end_with.
     * @param false|string $reportValue Value used by $reportCondition.
     * @param list<'email'|'mobile'|'slack'|'teams'> $reportMediums Delivery channels.
     *                                                Allowed values: email, mobile, slack, teams.
     * @param string $slackChannelID Slack channel ID when "slack" medium is enabled.
     * @param string $msTeamsWebhookUrl Microsoft Teams webhook URL when "teams" medium is enabled.
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
     * Delete alert by id.
     *
     * @param int $idAlert Alert ID to delete.
     * @throws \Exception In case alert does not exist or user has no permission to access alert.
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
     * @param string|array $idSites Website ID(s) to query.
     *                              Accepts comma-separated IDs, "all", numeric IDs as strings, or ["all"].
     *
     * @return list<array<string, mixed>> Triggered alerts for the current user and requested sites.
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
