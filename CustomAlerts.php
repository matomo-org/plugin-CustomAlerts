<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 */

namespace Piwik\Plugins\CustomAlerts;

use Piwik\Common;
use Piwik\Container\StaticContainer;
use Piwik\Piwik;
use Piwik\Plugin\Manager as PluginManager;
use Piwik\Plugins\SitesManager\API as SitesManagerApi;
use Piwik\Scheduler\Task;

/**
 *
 */
class CustomAlerts extends \Piwik\Plugin
{
    /**
     * @var null|string
     */
    public static $currentlyRunningScheduledTaskName = null;

    /**
     * @var int
     */
    public static $currentlyRunningScheduledTaskRetryCount = 0;

    public function registerEvents()
    {
        return array(
            'MobileMessaging.deletePhoneNumber'      => 'removePhoneNumberFromAllAlerts',
            'AssetManager.getJavaScriptFiles'        => 'getJavaScriptFiles',
            'AssetManager.getStylesheetFiles'        => 'getStylesheetFiles',
            'API.Request.dispatch'                   => 'checkApiPermission',
            'Request.dispatch'                       => 'checkControllerPermission',
            'Translate.getClientSideTranslationKeys' => 'getClientSideTranslationKeys',
            'UsersManager.deleteUser'                => 'deleteAlertsForLogin',
            'SitesManager.deleteSite.end'            => 'deleteAlertsForSite',
            'Db.getTablesInstalled'                  => 'getTablesInstalled',
            'ScheduledTasks.execute'                 => 'startingScheduledTask',
            'ScheduledTasks.execute.end'             => 'endingScheduledTask',
            'CustomAlerts.validateReportParameters'  => 'validateReportParameters',
        );
    }

    /**
     * Register the new tables, so Matomo knows about them.
     *
     * @param array $allTablesInstalled
     */
    public function getTablesInstalled(&$allTablesInstalled)
    {
        $allTablesInstalled[] = Common::prefixTable('alert');
        $allTablesInstalled[] = Common::prefixTable('alert_site');
        $allTablesInstalled[] = Common::prefixTable('alert_triggered');
    }

    public function checkApiPermission(&$parameters, $pluginName, $methodName)
    {
        if ($pluginName == 'CustomAlerts') {
            $this->checkPermission();
        }
    }

    private function checkPermission()
    {
        Piwik::checkUserIsNotAnonymous();
    }

    public function checkControllerPermission($module, $action)
    {
        if ($module != 'CustomAlerts') {
            return;
        }

        if ($action == 'formatAlerts') {
            throw new \Exception('This action does not exist');
        }

        $this->checkPermission();
    }

    public function install()
    {
        Model::install();
    }

    public function uninstall()
    {
        Model::uninstall();
    }

    public function getJavaScriptFiles(&$jsFiles)
    {
    }

    public function getStylesheetFiles(&$cssFiles)
    {
        $cssFiles[] = "plugins/CustomAlerts/stylesheets/alerts.less";
    }

    public function deleteAlertsForLogin($userLogin)
    {
        $model  = $this->getModel();
        $alerts = $this->getAllAlerts();

        foreach ($alerts as $alert) {
            if ($alert['login'] == $userLogin) {
                $model->deleteAlert($alert['idalert']);
            }
        }
    }

    private function getModel()
    {
        return new Model();
    }

    /**
     * @return array
     */
    private function getAllAlerts()
    {
        return $this->getModel()->getAllAlerts();
    }

    public function deleteAlertsForSite($idSite)
    {
        $model = $this->getModel();
        $model->deleteTriggeredAlertsForSite($idSite);

        $alerts = $this->getAllAlerts();

        foreach ($alerts as $alert) {
            $key = array_search($idSite, $alert['id_sites']);

            if (false !== $key) {
                unset($alert['id_sites'][$key]);
                $model->setSiteIds($alert['idalert'], array_values($alert['id_sites']));

                // TODO also delete logs
            }
        }
    }

    public function removePhoneNumberFromAllAlerts($phoneNumber)
    {
        $model  = $this->getModel();
        $alerts = $this->getAllAlerts();

        foreach ($alerts as $alert) {
            if (empty($alert['phone_numbers']) || !is_array($alert['phone_numbers'])) {
                continue;
            }

            $key = array_search($phoneNumber, $alert['phone_numbers'], true);

            if (false !== $key) {
                unset($alert['phone_numbers'][$key]);
                $model->updateAlert(
                    $alert['idalert'],
                    $alert['name'],
                    $alert['id_sites'],
                    $alert['period'],
                    $alert['email_me'],
                    $alert['additional_emails'],
                    array_values($alert['phone_numbers']),
                    $alert['metric'],
                    $alert['metric_condition'],
                    $alert['metric_matched'],
                    $alert['compared_to'],
                    $alert['report'],
                    $alert['report_condition'],
                    $alert['report_matched'],
                    $alert['report_mediums']
                );
            }
        }
    }

    public function getSiteIdsHavingAlerts()
    {
        $siteIds = SitesManagerApi::getInstance()->getAllSitesId();

        $model  = new Model();
        $alerts = $model->getAlerts($siteIds);

        $siteIdsHavingAlerts = array();
        foreach ($alerts as $alert) {
            $siteIdsHavingAlerts = array_merge($siteIdsHavingAlerts, $alert['id_sites']);
        }

        return array_values(array_unique($siteIdsHavingAlerts));
    }

    /**
     * If the task is for CustomAlerts, save the name of the task as static property so that we know what the currently
     * running task is.
     *
     * @param Task $task
     * @return void
     */
    public function startingScheduledTask(Task $task): void
    {
        if (strpos($taskName = $task->getName(), 'Piwik\Plugins\CustomAlerts\Tasks') === false) {
            return;
        }

        self::$currentlyRunningScheduledTaskName = $taskName;

        /** @var \Piwik\Scheduler\Timetable $timetable */
        $timetable = StaticContainer::getContainer()->get('Piwik\Scheduler\Timetable');
        // Look up the retry count so that we know whether this is a retry or not
        self::$currentlyRunningScheduledTaskRetryCount = $timetable->getRetryCount($taskName);
    }

    /**
     * If the task is for CustomAlerts, clear the property containing the name of the task that just ran.
     *
     * @param Task $task
     * @return void
     */
    public function endingScheduledTask(Task $task): void
    {
        if (strpos($task->getName(), 'Piwik\Plugins\CustomAlerts\Tasks') === false) {
            return;
        }

        self::$currentlyRunningScheduledTaskName = null;
        self::$currentlyRunningScheduledTaskRetryCount = 0;
    }

    public function getClientSideTranslationKeys(&$translations)
    {
        $translations[] = 'General_Value';
        $translations[] = 'CustomAlerts_InvalidMetricValue';
        $translations[] = 'General_Report';
        $translations[] = 'CustomAlerts_NoAlertsDefined';
        $translations[] = 'CustomAlerts_CreateNewAlert';
        $translations[] = 'CustomAlerts_AlertsHistory';
        $translations[] = 'CustomAlerts_AlertName';
        $translations[] = 'CustomAlerts_ApplyTo';
        $translations[] = 'ScheduledReports_SendReportTo';
        $translations[] = 'ScheduledReports_SentToMe';
        $translations[] = 'ScheduledReports_AlsoSendReportToTheseEmails';
        $translations[] = 'CustomAlerts_ThisAppliesTo';
        $translations[] = 'CustomAlerts_AlertCondition';
        $translations[] = 'CustomAlerts_When';
        $translations[] = 'CustomAlerts_AlertMeWhen';
        $translations[] = 'CustomAlerts_ComparedToThe';
        $translations[] = 'CustomAlerts_YouCanChoosePeriodFrom';
        $translations[] = 'CustomAlerts_PeriodDayDescription';
        $translations[] = 'CustomAlerts_PeriodWeekDescription';
        $translations[] = 'CustomAlerts_PeriodMonthDescription';
        $translations[] = 'MobileMessaging_PhoneNumbers';
        $translations[] = 'CustomAlerts_MobileMessagingPluginNotActivated';
        $translations[] = 'CustomAlerts_ManageAlerts';
        $translations[] = 'CustomAlerts_AreYouSureDeleteAlert';
        $translations[] = 'CustomAlerts_ThisAppliesToHelp';
        $translations[] = 'General_Yes';
        $translations[] = 'General_No';
        $translations[] = 'CustomAlerts_MediumTitle';
        $translations[] = 'CustomAlerts_MediumDescription';
    }

    public static function getReportMediumOptions(): array
    {
        return [
            ['key' => 'email', 'value' => Piwik::translate('CustomAlerts_MediumEmail'), 'disabled' => false],
            ['key' => 'mobile', 'value' => Piwik::translate('CustomAlerts_MediumMobile'), 'disabled' => !PluginManager::getInstance()->isPluginActivated('MobileMessaging')],
            ['key' => 'slack', 'value' => Piwik::translate('CustomAlerts_MediumSlack'), 'disabled' => !PluginManager::getInstance()->isPluginActivated('Slack')],
        ];
    }

    public function validateReportParameters($parameters, $alertMedium)
    {
        if ($alertMedium === 'email' && empty($parameters['emailMe']) && empty($parameters['additionalEmails'])) {
            throw new \Exception(Piwik::translate('CustomAlerts_InvalidEmailReportParameter'));
        } elseif ($alertMedium === 'mobile' && empty($parameters['phoneNumbers'])) {
            if (defined('PIWIK_TEST_MODE') && PIWIK_TEST_MODE) {
                return;
            }
            throw new \Exception(Piwik::translate('CustomAlerts_InvalidPhoneNumberReportParameter'));
        }
    }
}
