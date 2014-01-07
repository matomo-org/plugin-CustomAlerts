<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\CustomAlerts\tests;

use Piwik\Access;
use Piwik\Db;
use Piwik\Plugins\CustomAlerts\Model;
use Piwik\Plugins\CustomAlerts\API;
use Piwik\Translate;

/**
 * @group CustomAlerts
 * @group ModelTest
 * @group Database
 */
class ApiTest extends \DatabaseTestCase
{
    /**
     * @var \Piwik\Plugins\CustomAlerts\API
     */
    private $api;
    private $idSite;
    private $idSite2;

    public function setUp()
    {
        parent::setUp();

        Model::install();

        $this->api = API::getInstance();

        $this->setSuperUser();
        $this->idSite  = \Test_Piwik_BaseFixture::createWebsite('2012-08-09 11:22:33');
        $this->idSite2 = \Test_Piwik_BaseFixture::createWebsite('2012-08-10 11:22:33');
        $this->createAlert('Initial1', 'day');
        $this->createAlert('Initial2', 'week', array($this->idSite,$this->idSite2));
        $this->createAlert('Initial3', 'month', array($this->idSite2));
        $this->setUser();

        Translate::unloadEnglishTranslation();
    }

    public function tearDown()
    {
        Model::uninstall();

        parent::tearDown();
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage checkUserHasViewAccess Fake exception
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
        $this->setSuperUser();
        $this->createAlert('InvalidMetric', 'week', null, $metric = 'nb_notExisting');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Alerts_ReportOrMetricIsInvalid
     */
    public function test_addAlert_ShouldFail_IfInvalidReportProvided()
    {
        $this->setSuperUser();
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

        $this->assertIsAlert($id, 'MyCustomAlert', 'week');
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
     * @expectedException \Exception
     * @expectedExceptionMessage checkUserHasViewAccess Fake exception
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

        $this->assertIsAlert(2, 'MyCustomAlert', 'day', array(1));
    }

    public function test_getAlert_ShouldLoadAlertAndRelatedWebsiteIds_IfExists()
    {
        $this->setSuperUser();

        $this->assertIsAlert(1, 'Initial1', 'day', array(1));
        $this->assertIsAlert(2, 'Initial2', 'week', array(1,2));
        $this->assertIsAlert(3, 'Initial3', 'month', array(2));
    }

    public function test_getAlert_ShouldReturnDeletedAlerts()
    {
        $this->setSuperUser();

        $this->api->deleteAlert(1);
        $alert = $this->api->getAlert(1);
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

        $this->api->getAlert(9999);
    }

    public function test_getAlerts_shouldReturnAllAlertsThatMatchTheIdSites()
    {
        $this->setSuperUser();

        $alerts = $this->api->getAlerts(array($this->idSite));
        $this->assertCount(2, $alerts);
        $this->assertEquals('Initial1', $alerts[0]['name']);
        $this->assertEquals('Initial2', $alerts[1]['name']);

        $alerts = $this->api->getAlerts(array($this->idSite2));
        $this->assertCount(2, $alerts);
        $this->assertEquals('Initial2', $alerts[0]['name']);
        $this->assertEquals('Initial3', $alerts[1]['name']);

        $alerts = $this->api->getAlerts(array($this->idSite2, $this->idSite));
        $this->assertCount(3, $alerts);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage checkUserHasViewAccess Fake exception
     */
    public function test_getAlerts_shouldFail_IfUserDoesNotHaveAccessToWebsite()
    {
        $this->api->getAlerts(array($this->idSite));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage checkUserHasViewAccess Fake exception
     */
    public function test_getAllAlerts_shouldFail_IfUserIsNotTheSuperUser()
    {
        $this->api->getAlerts(array($this->idSite2, $this->idSite));
    }

    public function test_getAllAlerts_shouldReturnAllAlerts()
    {
        $this->setSuperUser();

        $alerts = $this->api->getAlerts(array($this->idSite2, $this->idSite));
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
        $this->api->getAlert(2);
    }

    public function test_deleteAlert_ShouldNotRemoveAlertButMarkItAsDeleted()
    {
        $this->setSuperUser();
        $this->api->deleteAlert(2);
        $alert = $this->api->getAlert(2);
        $this->assertEquals(1, $alert['deleted']);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage CustomAlerts_AccessException
     */
    public function test_deleteAlert_ShouldFail_IfNotOwnerOfAlertAndIfNotSuperUser()
    {
        $this->api->deleteAlert(2);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage CustomAlerts_AlertDoesNotExist
     */
    public function test_triggerAlert_shouldFail_IfAlertDoesNotExist()
    {
        $this->api->triggerAlert(99, 1, 94, 48);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage CustomAlerts_AccessException
     */
    public function test_triggerAlert_shouldFail_IfNotEnoughPermissions()
    {
        $this->api->triggerAlert(2, 1, 94, 48);
    }

    public function test_triggerAlert_getTriggeredAlerts_ShouldMarkAlertAsTriggeredForGivenWebsite()
    {
        $this->setSuperUser();

        $this->api->triggerAlert(2, 1, 94, 48);
        $triggeredAlerts = $this->api->getTriggeredAlerts('week', 'today', 'superUserLogin');

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
            'value_new' => 94,
            'value_old' => 48,
            'additional_emails' => array('test1@example.com', 'test2@example.com'),
            'phone_numbers' => array(),
            'email_me' => false,
            'idSites' => array(1, 2)
        );

        $this->assertEquals(array($expected), $triggeredAlerts);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage checkUserIsSuperUser Fake exception
     */
    public function test_triggerAlert_shouldVerifyWhetherUserIsActuallyTheUser()
    {
        $this->api->getTriggeredAlerts('InvaLiDPeriOd', 'today', 'superUserLogin');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage CustomAlerts_InvalidPeriod
     */
    public function test_triggerAlert_getTriggeredAlerts_ShouldFailIfPeriodIsInvalid()
    {
        $this->setSuperUser();

        $this->api->triggerAlert(2, 1, 99, 48);
        $triggeredAlerts = $this->api->getTriggeredAlerts('InvaLiDPeriOd', 'today', 'superUserLogin');

        $this->assertEquals(array(), $triggeredAlerts);
    }

    public function test_triggerAlert_getTriggeredAlerts_ShouldReturnAnAlertOnlyIfPeriodMatches()
    {
        $this->setSuperUser();

        $this->api->triggerAlert(2, 1, 99, 48);
        $triggeredAlerts = $this->api->getTriggeredAlerts('day', 'today', 'superUserLogin');

        $this->assertEquals(array(), $triggeredAlerts);
    }

    public function test_triggerAlert_getTriggeredAlerts_ShouldReturnAnAlertOnlyIfDateMatches()
    {
        $this->setSuperUser();

        $this->api->triggerAlert(1, 1, 99, 48);
        $triggeredAlerts = $this->api->getTriggeredAlerts('day', 'yesterday', 'superUserLogin');

        $this->assertEquals(array(), $triggeredAlerts);
    }

    public function test_getTriggeredAlerts_ShouldReturnAlertsForSuperUserByDefault()
    {
        $this->setSuperUser();

        $this->api->triggerAlert(1, 1, 99, 48);
        $triggeredAlerts = $this->api->getTriggeredAlerts('day', 'today', false);

        $this->assertCount(1, $triggeredAlerts);
    }

    private function createAlert($name, $period = 'week', $idSites = null, $metric = 'nb_visits', $report = 'MultiSites.getOne')
    {
        if (is_null($idSites)) {
            $idSites = $this->idSite;
        }

        $emails = array('test1@example.com', 'test2@example.com');
        $phoneNumbers = array();

        $id = $this->api->addAlert($name, $idSites, $period, 0, $emails, $phoneNumbers, $metric, 'less_than', 5, $report, 'matches_exactly', 'Piwik');
        return $id;
    }

    private function editAlert($id, $name, $period = 'week', $idSites = null, $metric = 'nb_visits', $report = 'MultiSites.getOne')
    {
        if (is_null($idSites)) {
            $idSites = $this->idSite;
        }

        $emails = array('test1@example.com', 'test2@example.com');
        $phoneNumbers = array();

        $id = $this->api->editAlert($id, $name, $idSites, $period, 0, $emails, $phoneNumbers, $metric, 'less_than', 5, $report, 'matches_exactly', 'Piwik');
        return $id;
    }

    private function assertIsAlert($id, $name, $period = 'week', $idSites = null, $login = 'superUserLogin', $metric = 'nb_visits', $metricCondition = 'less_than', $metricMatched = 5, $report = 'MultiSites.getOne', $reportCondition = 'matches_exactly', $reportMatched = 'Piwik')
    {
        if (is_null($idSites)) {
            $idSites = array($this->idSite);
        }

        $alert = $this->api->getAlert($id);

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
            'phone_numbers' => array(),
            'idSites' => $idSites,
            'deleted' => 0
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