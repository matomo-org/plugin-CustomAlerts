<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 */

namespace Piwik\Plugins\CustomAlerts;

use Piwik\Common;
use Piwik\Piwik;
use Piwik\Plugins\SitesManager\API as SitesManagerApi;

/**
 *
 */
class CustomAlerts extends \Piwik\Plugin
{

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
            'Db.getTablesInstalled'                  => 'getTablesInstalled'
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
                    $alert['report_matched']
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
        $translations[] = 'CustomAlerts_AlertsHistory';
        $translations[] = 'CustomAlerts_ManageAlerts';
        $translations[] = 'CustomAlerts_AreYouSureDeleteAlert';
        $translations[] = 'General_Yes';
        $translations[] = 'General_No';
    }
}
