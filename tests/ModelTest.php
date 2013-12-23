<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\CustomAlerts\tests;

use Piwik\Access;
use Piwik\Common;
use Piwik\Db;
use Piwik\Plugins\CustomAlerts\Model;
use Piwik\Translate;

/**
 * @group CustomAlerts
 * @group ModelTest
 * @group Database
 */
class ModelTest extends \DatabaseTestCase
{
    /**
     * @var \Piwik\Plugins\CustomAlerts\Model
     */
    private $model;
    private $idSite;
    private $idSite2;

    public function setUp()
    {
        parent::setUp();

        Model::install();

        $this->model = new Model();

        $this->setSuperUser();
        $this->idSite  = \Test_Piwik_BaseFixture::createWebsite('2012-08-09 11:22:33');
        $this->idSite2 = \Test_Piwik_BaseFixture::createWebsite('2012-08-10 11:22:33');
        $this->createAlert('Initial1', 'day');
        $this->createAlert('Initial2', 'week', array($this->idSite,$this->idSite2));
        $this->createAlert('Initial3', 'month', array($this->idSite2));
        $this->setUser();
    }

    public function tearDown()
    {
        Model::uninstall();

        parent::tearDown();
    }

    public function testInstall_ShouldNotFailAndActuallyCreateTheDatabases()
    {
        $this->assertContainTables(array('alert', 'alert_site', 'alert_log'));

        $columns = Db::fetchAll('show columns from ' . Common::prefixTable('alert'));
        $this->assertCount(12, $columns);

        $columns = Db::fetchAll('show columns from ' . Common::prefixTable('alert_site'));
        $this->assertCount(2, $columns);

        $columns = Db::fetchAll('show columns from ' . Common::prefixTable('alert_log'));
        $this->assertCount(3, $columns);
    }

    public function testUninstall_ShouldNotFailAndRemovesAllAlertTables()
    {
        Model::uninstall();

        $this->assertNotContainTables(array('alert', 'alert_site', 'alert_log'));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage General_ExceptionPrivilegeAccessWebsite
     */
    public function test_addAlert_ShouldFail_IfNotEnoughPermissions()
    {
        $this->createAlert('NotEnoughPermissions');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Alerts_ReportOrMetricIsInvalid
     */
    public function test_addAlert_ShouldFail_IfInvalidMetricProvided()
    {
        $this->createAlert('InvalidMetric', 'week', null, $metric = 'nb_notExisting');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Alerts_ReportOrMetricIsInvalid
     */
    public function test_addAlert_ShouldFail_IfInvalidReportProvided()
    {
        $this->createAlert('InvalidReport', 'week', null, 'nb_visits', 'IkReport.NotExisTing');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage CustomAlerts_InvalidPeriod
     */
    public function test_addAlert_ShouldFail_ShouldFailIfPeriodNotValid()
    {
        $this->setSuperUser();
        $this->createAlert('InvalidPeriod', 'unvAlidPerioD');
    }

    public function test_addAlert_ShouldCreateANewAlert()
    {
        $this->setSuperUser();

        $id = $this->createAlert('MyCustomAlert', 'week');
        $this->assertGreaterThan(3, $id);

        $this->assertCreatedAlert($id, 'MyCustomAlert', 'week');
    }

    public function test_addAlert_ShouldIncreaseId()
    {
        $this->setSuperUser();

        $firstId = $this->createAlert('MyCustomAlert', 'week');
        $id      = $this->createAlert('MyCustomAlert2', 'week');
        $this->assertEquals($firstId + 1, $id);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage CustomAlerts_AccessException
     */
    public function test_editAlert_ShouldFail_IfNotPermission()
    {
        $this->editAlert(2, 'MyCustomAlert', 'day');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage CustomAlerts_AlertDoesNotExist
     */
    public function test_editAlert_ShouldFail_IfNotExists()
    {
        $this->editAlert(99999, 'MyCustomAlert', 'day');
    }

    /**
     * @expectedException \Piwik\NoAccessException
     * @expectedExceptionMessage General_ExceptionPrivilegeAccessWebsite
     */
    public function test_editAlert_ShouldFail_IfNotPermissionForWebsites()
    {
        $id = $this->createAlert('MyAlert');
        $this->editAlert($id, 'MyCustomAlert', 'day', array(9999));
    }

    public function test_editAlert_ShouldUpdateExistingEntry()
    {
        $this->setSuperUser();

        $id = $this->editAlert(2, 'MyCustomAlert', 'day');
        $this->assertEquals(2, $id);

        $this->assertCreatedAlert(2, 'MyCustomAlert', 'day', array(1));
    }

    public function test_getAlert_ShouldLoadAlertAndRelatedWebsiteIds_IfExists()
    {
        $this->setSuperUser();

        $this->assertCreatedAlert(1, 'Initial1', 'day', array(1));
        $this->assertCreatedAlert(2, 'Initial2', 'week', array(1,2));
        $this->assertCreatedAlert(3, 'Initial3', 'month', array(2));
    }

    public function test_getAlert_ShouldReturnDeletedAlerts()
    {
        $this->setSuperUser();

        $this->model->deleteAlert(1);
        $alert = $this->model->getAlert(1);
        $this->assertEquals('Initial1', $alert['name']);
        $this->assertEquals(1, $alert['deleted']);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage CustomAlerts_AlertDoesNotExist
     */
    public function test_getAlert_ShouldFail_IfInvalidIdProvided()
    {
        $this->setSuperUser();

        $this->model->getAlert(9999);
    }

    public function test_getAlerts_shouldReturnAllAlertsThatMatchTheIdSites()
    {
        $this->setSuperUser();

        $alerts = $this->model->getAlerts(array($this->idSite));
        $this->assertCount(2, $alerts);
        $this->assertEquals('Initial1', $alerts[0]['name']);
        $this->assertEquals('Initial2', $alerts[1]['name']);

        $alerts = $this->model->getAlerts(array($this->idSite2));
        $this->assertCount(2, $alerts);
        $this->assertEquals('Initial2', $alerts[0]['name']);
        $this->assertEquals('Initial3', $alerts[1]['name']);

        $alerts = $this->model->getAlerts(array($this->idSite2, $this->idSite));
        $this->assertCount(3, $alerts);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage General_ExceptionPrivilege
     */
    public function test_getAlerts_shouldFail_IfUserDoesNotHaveAccessToWebsite()
    {
        $this->model->getAlerts(array($this->idSite));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage General_ExceptionPrivilege
     */
    public function test_getAllAlerts_shouldFail_IfUserIsNotTheSuperUser()
    {
        $this->model->getAlerts(array($this->idSite2, $this->idSite));
    }

    public function test_getAllAlerts_shouldReturnAllAlerts()
    {
        $this->setSuperUser();

        $alerts = $this->model->getAlerts(array($this->idSite2, $this->idSite));
        $this->assertCount(3, $alerts);
        $this->assertEquals('Initial1', $alerts[0]['name']);
        $this->assertEquals('Initial2', $alerts[1]['name']);
        $this->assertEquals('Initial3', $alerts[2]['name']);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage CustomAlerts_AccessException
     */
    public function test_getAlert_ShouldFail_IfNotOwnerOfAlertAndIfNotSuperUser()
    {
        $this->model->getAlert(2);
    }

    public function test_deleteAlert_ShouldNotRemoveAlertButMarkItAsDeleted()
    {
        $this->setSuperUser();
        $this->model->deleteAlert(2);
        $alert = $this->model->getAlert(2);
        $this->assertEquals(1, $alert['deleted']);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage CustomAlerts_AccessException
     */
    public function test_deleteAlert_ShouldFail_IfNotOwnerOfAlertAndIfNotSuperUser()
    {
        $this->model->deleteAlert(2);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage CustomAlerts_AlertDoesNotExist
     */
    public function test_triggerAlert_shouldFail_IfAlertDoesNotExist()
    {
        $this->model->triggerAlert(99, 1);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage CustomAlerts_AccessException
     */
    public function test_triggerAlert_shouldFail_IfNotEnoughPermissions()
    {
        $this->model->triggerAlert(2, 1);
    }

    public function test_triggerAlert_getTriggeredAlerts_ShouldMarkAlertAsTriggeredForGivenWebsite()
    {
        $this->setSuperUser();

        $this->model->triggerAlert(2, 1);
        $triggeredAlerts = $this->model->getTriggeredAlerts('week', 'today', 'superUserLogin');

        $this->assertCount(1, $triggeredAlerts);

        $this->assertNotEmpty($triggeredAlerts[0]['ts_triggered']);
        unset($triggeredAlerts[0]['ts_triggered']);

        $expected = array(
            'idalert' => 2,
            'idsite' => 1,
            'alert_name' => 'Initial2',
            'period' => 'week',
            'site_name' => 'Piwik test',
            'login' => 'superUserLogin',
            'report' => 'MultiSites.getOne',
            'report_condition' => 'matches_exactly',
            'report_matched' => 'Piwik',
            'metric' => 'nb_visits',
            'metric_condition' => 'less_than',
            'metric_matched' => '5'
        );

        $this->assertEquals(array($expected), $triggeredAlerts);
    }

    /**
     * @expectedException \Piwik\NoAccessException
     * @expectedExceptionMessage General_ExceptionCheckUserIsSuperUserOrTheUser
     */
    public function test_triggerAlert_shouldVerifyWhetherUserIsActuallyTheUser()
    {
        $this->model->getTriggeredAlerts('InvaLiDPeriOd', 'today', 'superUserLogin');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage CustomAlerts_InvalidPeriod
     */
    public function test_triggerAlert_getTriggeredAlerts_ShouldFailIfPeriodIsInvalid()
    {
        $this->setSuperUser();

        $this->model->triggerAlert(2, 1);
        $triggeredAlerts = $this->model->getTriggeredAlerts('InvaLiDPeriOd', 'today', 'superUserLogin');

        $this->assertEquals(array(), $triggeredAlerts);
    }

    public function test_triggerAlert_getTriggeredAlerts_ShouldReturnAnAlertOnlyIfPeriodMatches()
    {
        $this->setSuperUser();

        $this->model->triggerAlert(2, 1);
        $triggeredAlerts = $this->model->getTriggeredAlerts('day', 'today', 'superUserLogin');

        $this->assertEquals(array(), $triggeredAlerts);
    }

    public function test_triggerAlert_getTriggeredAlerts_ShouldReturnAnAlertOnlyIfDateMatches()
    {
        $this->setSuperUser();

        $this->model->triggerAlert(1, 1);
        $triggeredAlerts = $this->model->getTriggeredAlerts('day', 'yesterday', 'superUserLogin');

        $this->assertEquals(array(), $triggeredAlerts);
    }

    public function test_getTriggeredAlerts_ShouldReturnAlertsForSuperUserByDefault()
    {
        $this->setSuperUser();

        $this->model->triggerAlert(1, 1);
        $triggeredAlerts = $this->model->getTriggeredAlerts('day', 'today', false);

        $this->assertCount(1, $triggeredAlerts);
    }

    private function assertContainTables($expectedTables)
    {
        $tableNames = $this->getCurrentAvailableTableNames();

        foreach ($expectedTables as $expectedTable) {
            $this->assertContains(Common::prefixTable($expectedTable), $tableNames);
        }
    }

    private function assertNotContainTables($expectedTables)
    {
        $tableNames = $this->getCurrentAvailableTableNames();

        foreach ($expectedTables as $expectedTable) {
            $this->assertNotContains(Common::prefixTable($expectedTable), $tableNames);
        }
    }

    private function getCurrentAvailableTableNames()
    {
        $tables = Db::fetchAll('show tables');

        $tableNames = array();
        foreach ($tables as $table) {
            $tableNames[] = array_shift($table);
        }

        return $tableNames;
    }

    private function createAlert($name, $period = 'week', $idSites = null, $metric = 'nb_visits', $report = 'MultiSites.getOne')
    {
        if (is_null($idSites)) {
            $idSites = $this->idSite;
        }

        $id = $this->model->addAlert($name, $idSites, $period, 0, $metric, 'less_than', 5, $report, 'matches_exactly', 'Piwik');
        return $id;
    }

    private function editAlert($id, $name, $period = 'week', $idSites = null, $metric = 'nb_visits', $report = 'MultiSites.getOne')
    {
        if (is_null($idSites)) {
            $idSites = $this->idSite;
        }

        $id = $this->model->editAlert($id, $name, $idSites, $period, 0, $metric, 'less_than', 5, $report, 'matches_exactly', 'Piwik');
        return $id;
    }

    private function assertCreatedAlert($id, $name, $period = 'week', $idSites = null, $login = 'superUserLogin', $metric = 'nb_visits', $metricCondition = 'less_than', $metricMatched = 5, $report = 'MultiSites.getOne', $reportCondition = 'matches_exactly', $reportMatched = 'Piwik')
    {
        if (is_null($idSites)) {
            $idSites = array($this->idSite);
        }

        $alert = $this->model->getAlert($id);

        $expected = array(
            'idalert' => $id,
            'name' => $name,
            'login' => $login,
            'period' => $period,
            'report' => $report,
            'report_condition' => $reportCondition,
            'report_matched' => $reportMatched,
            'metric' => $metric,
            'metric_condition' => $metricCondition,
            'metric_matched' => $metricMatched,
            'enable_mail' => 0,
            'deleted' => 0,
            'idSites' => $idSites
        );

        $this->assertEquals($expected, $alert);
    }

    private function setSuperUser()
    {
        $pseudoMockAccess = new \FakeAccess();
        \FakeAccess::setIdSitesAdmin(array(1, 2));
        \FakeAccess::$superUser = true;
        \FakeAccess::$identity = 'superUserLogin';
        Access::setSingletonInstance($pseudoMockAccess);
    }

    private function setUser()
    {
        $pseudoMockAccess = new \FakeAccess;
        \FakeAccess::setSuperUser(false);
        \FakeAccess::$idSitesView = array(99);
        \FakeAccess::$identity = 'aUser';
        Access::setSingletonInstance($pseudoMockAccess);
    }

}