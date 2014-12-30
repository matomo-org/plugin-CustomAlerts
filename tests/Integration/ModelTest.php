<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\CustomAlerts\tests\Integration;

use Piwik\Common;
use Piwik\Date;
use Piwik\Db;
use Piwik\Plugins\CustomAlerts\Model;

/**
 * @group CustomAlerts
 * @group ModelTest
 * @group Plugins
 */
class ModelTest extends BaseTest
{

    public function setUp()
    {
        parent::setUp();

        $this->createAlert('Initial1', 'day');
        $this->createAlert('Initial2', 'week', array($this->idSite,$this->idSite2));
        $this->createAlert('Initial3', 'month', array($this->idSite2));
        $this->setUser();
    }

    public function test_install_ShouldNotFailAndActuallyCreateTheDatabases()
    {
        $this->assertContainTables(array('alert', 'alert_site', 'alert_triggered'));

        $columns = Db::fetchAll('show columns from ' . Common::prefixTable('alert'));
        $this->assertCount(14, $columns);

        $columns = Db::fetchAll('show columns from ' . Common::prefixTable('alert_site'));
        $this->assertCount(2, $columns);

        $columns = Db::fetchAll('show columns from ' . Common::prefixTable('alert_triggered'));
        $this->assertCount(20, $columns);
    }

    public function test_uninstall_ShouldNotFailAndRemovesAllAlertTables()
    {
        Model::uninstall();

        $this->assertNotContainTables(array('alert', 'alert_site', 'alert_triggered'));

        Model::install();
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

    public function test_setSiteId_ShouldUpdateExistingSiteIds()
    {
        $this->model->setSiteIds(1, array(1));
        $alert = $this->model->getAlert(1);
        $this->assertEquals(array(1), $alert['id_sites']);

        $this->model->setSiteIds(1, array());
        $alert = $this->model->getAlert(1);
        $this->assertEquals(array(), $alert['id_sites']);

        $this->model->setSiteIds(1, array(1, 2));
        $alert = $this->model->getAlert(1);
        $this->assertEquals(array(1, 2), $alert['id_sites']);
    }

    public function test_setSiteId_ShouldNotFail_IfAlertDoesNotExist()
    {
        $this->assertNull($this->model->setSiteIds(999995, array(1)));
    }

    public function test_getAlert_ShouldLoadAlertAndRelatedWebsiteIds_IfExists()
    {
        $this->assertIsAlert(1, 'Initial1', 'day', array(1));
        $this->assertIsAlert(2, 'Initial2', 'week', array(1,2));
        $this->assertIsAlert(3, 'Initial3', 'month', array(2));
    }

    public function test_getAlerts_shouldReturnAllAlertsThatMatchTheIdSitesAndLogin()
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

        $alerts = $this->model->getAlerts(array($this->idSite2, $this->idSite), 'unknownuser');
        $this->assertCount(0, $alerts);

        $this->createAlert('myname', 'week', array($this->idSite), 'nb_visits', 'MultiSites_getOne', 'unknownuser');
        $alerts = $this->model->getAlerts(array($this->idSite2, $this->idSite), 'unknownuser');
        $this->assertCount(1, $alerts);
    }

    public function test_getAllAlerts_shouldReturnAllAlerts()
    {
        $alerts = $this->model->getAllAlerts();
        $this->assertCount(3, $alerts);
        $this->assertEquals('Initial1', $alerts[0]['name']);
        $this->assertEquals('Initial2', $alerts[1]['name']);
        $this->assertEquals('Initial3', $alerts[2]['name']);
    }

    public function test_getAllAlerts_shouldReturnAllAlertsHavingSamePeriod()
    {
        $this->createAlert('Custom', 'week', array());
        $alerts = $this->model->getAllAlertsForPeriod('week');
        $this->assertCount(2, $alerts);
        $this->assertEquals('Initial2', $alerts[0]['name']);
        $this->assertEquals('Custom', $alerts[1]['name']);

        $alerts = $this->model->getAllAlertsForPeriod('day');
        $this->assertCount(1, $alerts);
        $this->assertEquals('Initial1', $alerts[0]['name']);
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

    public function test_triggerAlert_getTriggeredAlertsForPeriod_ShouldMarkAlertAsTriggeredForGivenWebsite()
    {
        $this->model->triggerAlert(2, 1, 99, 48.519, Date::now()->getDatetime());
        $triggeredAlerts = $this->model->getTriggeredAlertsForPeriod('week', 'today', 'superUserLogin');

        $this->assertCount(1, $triggeredAlerts);

        $this->assertNotEmpty($triggeredAlerts[0]['ts_triggered']);
        unset($triggeredAlerts[0]['ts_triggered']);

        $expected = array(
            'idtriggered' => 1,
            'idalert' => 2,
            'idsite' => 1,
            'ts_last_sent' => null,
            'name' => 'Initial2',
            'period' => 'week',
            'login' => 'superUserLogin',
            'report' => 'MultiSites_getOne',
            'report_condition' => 'matches_exactly',
            'report_matched' => 'Piwik',
            'metric' => 'nb_visits',
            'metric_condition' => 'less_than',
            'metric_matched' => '5',
            'compared_to' => 1,
            'value_new' => 99,
            'value_old' => 48.519,
            'additional_emails' => array('test1@example.com', 'test2@example.com'),
            'phone_numbers' => array('0123456789'),
            'email_me' => 0,
            'id_sites' => array(1, 2)
        );

        $this->assertEquals(array($expected), $triggeredAlerts);
    }

    public function test_triggerAlert_ShouldIncreaseId()
    {
        $this->model->triggerAlert(2, 1, 99, 48.519, Date::now()->getDatetime());
        $this->model->triggerAlert(2, 1, 99, 48.519, Date::now()->getDatetime());

        $triggeredAlerts = $this->model->getTriggeredAlertsForPeriod('week', 'today', 'superUserLogin');

        $this->assertCount(2, $triggeredAlerts);

        $this->assertEquals(1, $triggeredAlerts[0]['idtriggered']);
        $this->assertEquals(2, $triggeredAlerts[1]['idtriggered']);
    }

    public function test_getTriggeredAlerts_ShouldReturnAllThatMatchesLoginAndIdSite()
    {
        $idSite = 1;

        $this->model->triggerAlert(2, $idSite, 99, 48, Date::now()->getDatetime());

        $triggeredAlerts = $this->model->getTriggeredAlerts(array($idSite), 'superUserLogin');
        $this->assertCount(1, $triggeredAlerts);

        $triggeredAlerts = $this->model->getTriggeredAlerts(array($idSite, 2), 'superUserLogin');
        $this->assertCount(1, $triggeredAlerts);

        $triggeredAlerts = $this->model->getTriggeredAlerts(array(3), 'superUserLogin');
        $this->assertCount(0, $triggeredAlerts);

        $triggeredAlerts = $this->model->getTriggeredAlerts(array($idSite), 'nonmatchinglogin');
        $this->assertCount(0, $triggeredAlerts);
    }

    public function test_getTriggeredAlertsForPeriod_ShouldReturnAnAlertOnlyIfPeriodMatches()
    {
        $this->model->triggerAlert(2, 1, 99, 48, Date::now()->getDatetime());
        $triggeredAlerts = $this->model->getTriggeredAlertsForPeriod('day', 'today');

        $this->assertEquals(array(), $triggeredAlerts);
    }

    public function test_getTriggeredAlertsForPeriod_ShouldReturnAnAlertOnlyIfDateMatches()
    {
        $this->model->triggerAlert(1, 1, 99, 48, Date::now()->getDatetime());
        $triggeredAlerts = $this->model->getTriggeredAlertsForPeriod('day', 'yesterday');

        $this->assertEquals(array(), $triggeredAlerts);
    }

    public function test_getTriggeredAlertsForPeriod_ShouldReturnAllAlertsThatMatchesDateAndPeriod()
    {
        $this->model->triggerAlert(1, 1, 99, 48, Date::now()->getDatetime());
        $triggeredAlerts = $this->model->getTriggeredAlertsForPeriod('day', 'today');

        $this->assertCount(1, $triggeredAlerts);
    }

    public function test_markTriggeredAlertAsSent_shouldSetTsLastSent()
    {
        $this->model->triggerAlert(1, 1, 99, 48, Date::now()->getDatetime());
        $this->model->markTriggeredAlertAsSent(1, 1389301798);

        // verify
        $triggeredAlerts = $this->model->getTriggeredAlertsForPeriod('day', 'today');
        $this->assertEquals('2014-01-09 21:09:58', $triggeredAlerts[0]['ts_last_sent']);
    }

    public function test_markTriggeredAlertAsSent_shouldNotSetTsLastSent_IfSiteIdDoesNotMatch()
    {
        $this->model->triggerAlert(1, 1, 99, 48, Date::now()->getDatetime());
        $this->model->markTriggeredAlertAsSent(3, 1389301798);

        // verify
        $triggeredAlerts = $this->model->getTriggeredAlertsForPeriod('day', 'today');
        $this->assertNull($triggeredAlerts[0]['ts_last_sent']);
    }

    public function test_deleteTriggeredAlertsForSite()
    {
        $this->model->triggerAlert(1, 1, 99, 48, Date::now()->getDatetime());
        $this->model->triggerAlert(1, 2, 99, 48, Date::now()->getDatetime());
        $this->model->triggerAlert(1, 3, 99, 48, Date::now()->getDatetime());
        $this->model->triggerAlert(1, 2, 99, 48, Date::now()->getDatetime());

        $alerts = $this->model->getTriggeredAlerts(array(1, 2, 3), 'superUserLogin');
        $this->assertCount(4, $alerts);

        $this->model->deleteTriggeredAlertsForSite(2);

        // verify actually removed the correct ones
        $alerts = $this->model->getTriggeredAlerts(array(1, 2, 3), 'superUserLogin');
        $this->assertCount(2, $alerts);

        $this->assertEquals(1, $alerts[0]['idtriggered']);
        $this->assertEquals(3, $alerts[1]['idtriggered']);
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

    private function createAlert($name, $period = 'week', $idSites = null, $metric = 'nb_visits', $report = 'MultiSites_getOne', $login = 'superUserLogin')
    {
        if (is_null($idSites)) {
            $idSites = $this->idSite;
        }
        if (!is_array($idSites)) {
            $idSites = array($idSites);
        }

        $emails = array('test1@example.com', 'test2@example.com');
        $phoneNumbers = array('0123456789');

        $id = $this->model->createAlert($name, $idSites, $login, $period, 0, $emails, $phoneNumbers, $metric, 'less_than', 5, $comparedTo = 1, $report, 'matches_exactly', 'Piwik');
        return $id;
    }

    private function editAlert($id, $name, $period = 'week', $idSites = null, $metric = 'nb_visits', $report = 'MultiSites_getOne')
    {
        if (is_null($idSites)) {
            $idSites = $this->idSite;
        }
        if (!is_array($idSites)) {
            $idSites = array($idSites);
        }

        $emails = array('test1@example.com', 'test2@example.com');
        $phoneNumbers = array('0123456789');

        $id = $this->model->updateAlert($id, $name, $idSites, $period, 0, $emails, $phoneNumbers, $metric, 'less_than', 5, $comparedTo = 1, $report, 'matches_exactly', 'Piwik');
        return $id;
    }

    private function assertIsAlert($id, $name, $period = 'week', $idSites = null, $login = 'superUserLogin', $metric = 'nb_visits', $metricCondition = 'less_than', $metricMatched = 5, $report = 'MultiSites_getOne', $reportCondition = 'matches_exactly', $reportMatched = 'Piwik')
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
            'compared_to' => 1,
            'id_sites' => $idSites,
        );

        $this->assertEquals($expected, $alert);
    }

}
