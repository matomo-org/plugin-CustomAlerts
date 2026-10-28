<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\CustomAlerts\tests\Integration;

use Piwik\Common;
use Piwik\Container\StaticContainer;
use Piwik\Db;
use Piwik\Plugin\Manager;
use Piwik\Plugins\CustomAlerts\Updates_6_0_1;
use Piwik\Tests\Framework\Fixture;
use Piwik\Updater;

require_once PIWIK_INCLUDE_PATH . '/plugins/CustomAlerts/Updates/6.0.1.php';

/**
 * @group CustomAlerts
 * @group UpdatesTest
 * @group Plugins
 */
class UpdatesTest extends BaseTest
{
    private $idSiteWithEcommerce;

    private $idSiteWithEcommerce2;

    public function setUp(): void
    {
        parent::setUp();

        $this->idSiteWithEcommerce  = Fixture::createWebsite('2012-08-11 11:22:33', $ecommerce = 1);
        $this->idSiteWithEcommerce2 = Fixture::createWebsite('2012-08-12 11:22:33', $ecommerce = 1);
    }

    public function test_update_shouldMoveGoalsOverviewToTheEcommerceOrderReport_IfAllSitesHaveEcommerce()
    {
        $idAlert = $this->storeAlert('Goals_get_idGoal--0', [$this->idSiteWithEcommerce, $this->idSiteWithEcommerce2]);

        $this->runUpdate();

        $this->assertStoredReport('Goals_get_idGoal--ecommerceOrder', $idAlert);
    }

    public function test_update_shouldMoveGoalsOverviewToTheEcommerceOrderReport_IfAllSitesHaveEcommerce_EvenWhileTheEcommercePluginIsInactive()
    {
        $idAlert = $this->storeAlert('Goals_get_idGoal--0', [$this->idSiteWithEcommerce, $this->idSiteWithEcommerce2]);

        Manager::getInstance()->loadPlugins(array_diff(Manager::getInstance()->getActivatedPlugins(), ['Ecommerce']));

        $this->runUpdate();

        $this->assertStoredReport('Goals_get_idGoal--ecommerceOrder', $idAlert);
    }

    public function test_update_shouldMoveGoalsOverviewToTheAllGoalsReport_IfNoSiteHasEcommerce()
    {
        $idAlert = $this->storeAlert('Goals_get_idGoal--0', [$this->idSite]);

        $this->runUpdate();

        $this->assertStoredReport('Goals_get', $idAlert);
    }

    public function test_update_shouldMoveGoalsOverviewToTheAllGoalsReport_IfOnlySomeSitesHaveEcommerce()
    {
        $idAlert = $this->storeAlert('Goals_get_idGoal--0', [$this->idSite, $this->idSiteWithEcommerce]);

        $this->runUpdate();

        $this->assertStoredReport('Goals_get', $idAlert);
    }

    public function test_update_shouldMoveTheConversionDistributionsToTheirAllGoalsReports()
    {
        $idAlertVisits = $this->storeAlert('Goals_getVisitsUntilConversion_idGoal--0', [$this->idSiteWithEcommerce]);
        $idAlertDays   = $this->storeAlert('Goals_getDaysToConversion_idGoal--0', [$this->idSite]);

        $this->runUpdate();

        $this->assertStoredReport('Goals_getVisitsUntilConversion', $idAlertVisits);
        $this->assertStoredReport('Goals_getDaysToConversion', $idAlertDays);
    }

    public function test_update_shouldKeepOtherReports()
    {
        $idAlertGoals = $this->storeAlert('Goals_get', [$this->idSiteWithEcommerce]);
        $idAlertSites = $this->storeAlert('MultiSites_getOne', [$this->idSite], 'nb_visits');

        $this->runUpdate();

        $this->assertStoredReport('Goals_get', $idAlertGoals);
        $this->assertStoredReport('MultiSites_getOne', $idAlertSites);
    }

    public function test_update_shouldKeepTriggeredAlertsOnTheReportTheyWereTriggeredFor()
    {
        $idAlertEcommerce = $this->storeAlert('Goals_get_idGoal--0', [$this->idSiteWithEcommerce, $this->idSiteWithEcommerce2]);
        $idAlertAllGoals  = $this->storeAlert('Goals_get_idGoal--0', [$this->idSite, $this->idSiteWithEcommerce]);
        $idAlertDays      = $this->storeAlert('Goals_getDaysToConversion_idGoal--0', [$this->idSiteWithEcommerce]);

        $this->triggerAlert($idAlertEcommerce, $this->idSiteWithEcommerce);
        $this->triggerAlert($idAlertAllGoals, $this->idSite);
        $this->triggerAlert($idAlertAllGoals, $this->idSiteWithEcommerce);
        $this->triggerAlert($idAlertDays, $this->idSiteWithEcommerce);

        $this->runUpdate();

        $this->assertStoredReport('Goals_get_idGoal--ecommerceOrder', $idAlertEcommerce);
        $this->assertTriggeredReports(['Goals_get_idGoal--0'], $idAlertEcommerce);
        $this->assertTriggeredReports(['Goals_get_idGoal--0', 'Goals_get_idGoal--0'], $idAlertAllGoals);
        $this->assertTriggeredReports(['Goals_getDaysToConversion_idGoal--0'], $idAlertDays);
    }

    public function test_update_shouldHaveNothingToDo_IfOnlyTriggeredAlertsUseRetiredReports()
    {
        $idAlert = $this->storeAlert('Goals_get_idGoal--0', [$this->idSiteWithEcommerce]);
        $this->triggerAlert($idAlert, $this->idSiteWithEcommerce);
        $this->setStoredReport('Goals_get_idGoal--ecommerceOrder', $idAlert);

        $this->assertSame([], $this->makeUpdate()->getMigrations(new Updater()));
    }

    public function test_update_shouldHaveNothingToDo_OnceAlertsAreMoved()
    {
        $this->storeAlert('Goals_get_idGoal--0', [$this->idSiteWithEcommerce]);

        $this->runUpdate();

        $this->assertSame([], $this->makeUpdate()->getMigrations(new Updater()));
    }

    private function storeAlert($report, array $idSites, $metric = 'nb_conversions')
    {
        return $this->model->createAlert('Alert', $idSites, 'superUserLogin', 'week', 1, [], [], $metric, 'less_than', 5, 1, $report, null, null, ['email'], '', '', '');
    }

    private function triggerAlert($idAlert, $idSite)
    {
        $this->model->triggerAlert($idAlert, $idSite, 10, 20, '2012-08-15 10:00:00');
    }

    private function setStoredReport($report, $idAlert)
    {
        Db::query('UPDATE ' . Common::prefixTable('alert') . ' SET report = ? WHERE idalert = ?', [$report, $idAlert]);
    }

    private function assertTriggeredReports(array $expected, $idAlert)
    {
        $reports = Db::get()->fetchCol(
            'SELECT report FROM ' . Common::prefixTable('alert_triggered') . ' WHERE idalert = ? ORDER BY idtriggered',
            [$idAlert]
        );

        $this->assertSame($expected, $reports);
    }

    private function assertStoredReport($expected, $idAlert)
    {
        $this->assertSame($expected, $this->model->getAlert($idAlert)['report']);
    }

    private function runUpdate()
    {
        $this->makeUpdate()->doUpdate(new Updater());
    }

    private function makeUpdate(): Updates_6_0_1
    {
        return StaticContainer::get(Updates_6_0_1::class);
    }
}
