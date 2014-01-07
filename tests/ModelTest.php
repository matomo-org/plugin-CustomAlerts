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

        $this->setSuperUser();
        $this->model = new Model();

        $this->idSite  = \Test_Piwik_BaseFixture::createWebsite('2012-08-09 11:22:33');
        $this->idSite2 = \Test_Piwik_BaseFixture::createWebsite('2012-08-10 11:22:33');
        $this->createAlert('Initial1', 'day');
        $this->createAlert('Initial2', 'week', array($this->idSite, $this->idSite2));
        $this->createAlert('Initial3', 'month', array($this->idSite2));
        Translate::unloadEnglishTranslation();
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
        $this->assertCount(13, $columns);

        $columns = Db::fetchAll('show columns from ' . Common::prefixTable('alert_site'));
        $this->assertCount(2, $columns);

        $columns = Db::fetchAll('show columns from ' . Common::prefixTable('alert_log'));
        $this->assertCount(5, $columns);
    }

    public function testUninstall_ShouldNotFailAndRemovesAllAlertTables()
    {
        Model::uninstall();

        $this->assertNotContainTables(array('alert', 'alert_site', 'alert_log'));
    }

    public function test_addAlert_ShouldCreateANewAlert()
    {
        $id = $this->createAlert('MyCustomAlert', 'week');
        $this->assertGreaterThan(3, $id);

        $this->assertIsAlert($id, 'MyCustomAlert', 'week');
    }

    public function test_addAlert_ShouldIncreaseId()
    {
        $firstId = $this->createAlert('MyCustomAlert', 'week');
        $id      = $this->createAlert('MyCustomAlert2', 'week');
        $this->assertEquals($firstId + 1, $id);
    }

    public function test_editAlert_ShouldUpdateExistingEntry()
    {
        $id = $this->editAlert(2, 'MyCustomAlert', 'day');
        $this->assertEquals(2, $id);

        $this->assertIsAlert(2, 'MyCustomAlert', 'day', array(1));
    }

    public function test_getAlert_ShouldLoadAlertAndRelatedWebsiteIds_IfExists()
    {
        $this->assertIsAlert(1, 'Initial1', 'day', array(1));
        $this->assertIsAlert(2, 'Initial2', 'week', array(1,2));
        $this->assertIsAlert(3, 'Initial3', 'month', array(2));
    }

    public function test_getAlerts_shouldReturnAllAlertsThatMatchTheIdSites()
    {
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

    public function test_getAllAlerts_shouldReturnAllAlerts()
    {
        $alerts = $this->model->getAlerts(array($this->idSite2, $this->idSite));
        $this->assertCount(3, $alerts);
        $this->assertEquals('Initial1', $alerts[0]['name']);
        $this->assertEquals('Initial2', $alerts[1]['name']);
        $this->assertEquals('Initial3', $alerts[2]['name']);
    }

    public function test_deleteAlert_ShouldNotReallyRemoveTheAlert()
    {
        // make sure there is an entry that we delete
        $alert = $this->model->getAlert(2);
        $this->assertNotEmpty($alert);

        $this->model->deleteAlert(2);

        $alert = $this->model->getAlert(2);
        $this->assertEmpty($alert);
    }

    public function test_triggerAlert_getTriggeredAlerts_ShouldMarkAlertAsTriggeredForGivenWebsite()
    {
        $this->model->triggerAlert(2, 1, 99, 48);
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
            'metric_matched' => '5',
            'value_new' => 99,
            'value_old' => 48,
            'additional_emails' => array('test1@example.com', 'test2@example.com'),
            'phone_numbers' => array('0123456789'),
            'email_me' => false,
            'idSites' => array(1, 2)
        );

        $this->assertEquals(array($expected), $triggeredAlerts);
    }

    public function test_getTriggeredAlerts_ShouldReturnAnAlertOnlyIfPeriodMatches()
    {
        $this->model->triggerAlert(2, 1, 99, 48);
        $triggeredAlerts = $this->model->getTriggeredAlerts('day', 'today', 'superUserLogin');

        $this->assertEquals(array(), $triggeredAlerts);
    }

    public function test_getTriggeredAlerts_ShouldReturnAnAlertOnlyIfDateMatches()
    {
        $this->model->triggerAlert(1, 1, 99, 48);
        $triggeredAlerts = $this->model->getTriggeredAlerts('day', 'yesterday', 'superUserLogin');

        $this->assertEquals(array(), $triggeredAlerts);
    }

    public function test_getTriggeredAlerts_ShouldReturnAllAlerts_IfLoginIsFalse()
    {
        $this->model->triggerAlert(1, 1, 99, 48);
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
        if (!is_array($idSites)) {
            $idSites = array($idSites);
        }

        $emails = array('test1@example.com', 'test2@example.com');
        $phoneNumbers = array('0123456789');

        $id = $this->model->addAlert($name, $idSites, $period, 0, $emails, $phoneNumbers, $metric, 'less_than', 5, $report, 'matches_exactly', 'Piwik');
        return $id;
    }

    private function editAlert($id, $name, $period = 'week', $idSites = null, $metric = 'nb_visits', $report = 'MultiSites.getOne')
    {
        if (is_null($idSites)) {
            $idSites = $this->idSite;
        }
        if (!is_array($idSites)) {
            $idSites = array($idSites);
        }

        $emails = array('test1@example.com', 'test2@example.com');
        $phoneNumbers = array('0123456789');

        $id = $this->model->editAlert($id, $name, $idSites, $period, 0, $emails, $phoneNumbers, $metric, 'less_than', 5, $report, 'matches_exactly', 'Piwik');
        return $id;
    }

    private function assertIsAlert($id, $name, $period = 'week', $idSites = null, $login = 'superUserLogin', $metric = 'nb_visits', $metricCondition = 'less_than', $metricMatched = 5, $report = 'MultiSites.getOne', $reportCondition = 'matches_exactly', $reportMatched = 'Piwik')
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
            'email_me' => 0,
            'additional_emails' => array('test1@example.com', 'test2@example.com'),
            'phone_numbers' => array('0123456789'),
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

}