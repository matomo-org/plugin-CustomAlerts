<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\CustomAlerts\tests\Integration;

use Piwik\Date;
use Piwik\Tests\Framework\Mock\FakeAccess;

/**
 * @group CustomAlerts
 * @group ApiTest
 * @group Plugins
 */
class ApiTest extends BaseTest
{

    public function setUp(): void
    {
        parent::setUp();

        $this->createAlert('Initial1', 'day');
        $this->createAlert('Initial2', 'week', array($this->idSite, $this->idSite2));
        $this->createAlert('Initial3', 'month', array($this->idSite2));
        $this->setUser();
    }

    public function test_addAlert_ShouldFail_IfNotEnoughPermissions()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('checkUserHasViewAccess Fake exception');

        $this->createAlert('NotEnoughPermissions');
    }

    public function test_addAlert_ShouldFail_IfEmptyName()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('General_PleaseSpecifyValue');

        $this->setSuperUser();
        $this->createAlert('');
    }

    public function test_addAlert_ShouldFail_IfInvalidMetricProvided()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CustomAlerts_InvalidMetric');

        $this->setSuperUser();
        $this->createAlert('InvalidMetric', 'week', null, $metric = 'nb_notExisting');
    }

    public function test_addAlert_ShouldFail_IfInvalidReportProvided()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CustomAlerts_InvalidReport');

        $this->setSuperUser();
        $this->createAlert('InvalidReport', 'week', null, 'nb_visits', 'IkReport_NotExisTing');
    }

    public function test_addAlert_ShouldFail_ShouldFailIfPeriodNotValid()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CustomAlerts_InvalidPeriod');

        $this->setSuperUser();
        $this->createAlert('InvalidPeriod', 'unvAlidPerioD');
    }

    public function test_addAlert_ShouldFail_IfInvalidMetricCondition()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CustomAlerts_InvalidMetricCondition');

        $this->setSuperUser();
        $this->createAlert('InvalidMetricCondition', 'week', null, 'nb_visits', 'MultiSites_getOne', 'InvaLiD');
    }

    public function test_addAlert_ShouldFail_IfEmptyMetricCondition()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CustomAlerts_InvalidMetricCondition');

        $this->setSuperUser();
        $this->createAlert('EmptyMetricCondition', 'week', null, 'nb_visits', 'MultiSites_getOne', '');
    }

    public function test_addAlert_ShouldFail_IfInvalidReportCondition()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CustomAlerts_InvalidReportCondition');

        $this->setSuperUser();
        $this->createAlert('InvalidReportCondition', 'week', null, 'nb_visits', 'MultiSites_getOne', 'less_than', 'InvaLiD');
    }

    public function test_addAlert_ShouldFail_IfInvalidComparableDate()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CustomAlerts_InvalidComparableDate');

        $this->setSuperUser();
        $this->createAlert('InvalidComparableDate', 'week', null, 'nb_visits', 'MultiSites_getOne', 'less_than', 'matches_exactly', array(), 99);
    }

    public function test_addAlert_ShouldFail_IfInvalidEmail()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('UsersManager_ExceptionInvalidEmail (inv+34i32s?y)');

        $this->setSuperUser();
        $this->createAlert('InvalidEmail', 'week', null, 'nb_visits', 'MultiSites_getOne', 'less_than', 'matches_any', array('test@example.com', 'inv+34i32s?y', 'test2@example.com'));
    }

    public function test_addAlert_ShouldCreateANewAlert()
    {
        $this->setSuperUser();

        $id = $this->createAlert('MyCustomAlert', 'week');
        $this->assertGreaterThan(3, $id);

        $this->assertIsAlert($id, 'MyCustomAlert', 'week');
    }

    public function test_editAlert_ShouldFail_IfNotPermission()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CustomAlerts_AccessException');

        $this->editAlert(2, 'MyCustomAlert', 'day');
    }

    public function test_editAlert_ShouldFail_IfNotExists()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CustomAlerts_AlertDoesNotExist');

        $this->editAlert(99999, 'MyCustomAlert', 'day');
    }

    public function test_editAlert_ShouldFail_IfNotPermissionForWebsites()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('checkUserHasViewAccess Fake exception');

        $id = $this->createAlert('MyAlert');
        $this->editAlert($id, 'MyCustomAlert', 'day', array(9999));
    }

    public function test_editAlert_ShouldFail_IfInvalidMetricCondition()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CustomAlerts_InvalidMetricCondition');

        $this->setSuperUser();

        $id = $this->createAlert('MyAlert');
        $this->editAlert($id, 'InvalidMetricCondition', 'week', null, 'nb_visits', 'MultiSites_getOne', 'InvaLiD');
    }

    public function test_editAlert_ShouldFail_IfEmptyMetricCondition()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CustomAlerts_InvalidMetricCondition');

        $this->setSuperUser();

        $id = $this->createAlert('MyAlert');
        $this->editAlert($id, 'EmptyMetricCondition', 'week', null, 'nb_visits', 'MultiSites_getOne', '');
    }

    public function test_editAlert_ShouldFail_IfInvalidReportCondition()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CustomAlerts_InvalidReportCondition');

        $this->setSuperUser();

        $id = $this->createAlert('MyAlert');
        $this->editAlert($id, 'InvalidReportCondition', 'week', null, 'nb_visits', 'MultiSites_getOne', 'less_than', 'InvaLiD');
    }

    public function test_editAlert_ShouldFail_IfInvalidEmail()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('UsersManager_ExceptionInvalidEmail (inv+34i32s?y)');

        $this->setSuperUser();

        $id = $this->createAlert('MyAlert');
        $this->editAlert($id, 'InvalidEmail', 'week', null, 'nb_visits', 'MultiSites_getOne', 'less_than', 'matches_any', array('test@example.com', 'inv+34i32s?y', 'test2@example.com'));
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
        $this->assertIsAlert(2, 'Initial2', 'week', array(1, 2));
        $this->assertIsAlert(3, 'Initial3', 'month', array(2));
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

    public function test_getAlerts_shouldReturnOnlyAlertsThatMatchTheLogin()
    {
        $siteIds = array($this->idSite2, $this->idSite);

        FakeAccess::$idSitesView = $siteIds;

        $alerts = $this->api->getAlerts($siteIds);
        $this->assertCount(0, $alerts);

        FakeAccess::$identity = 'superUserLogin';
        $alerts               = $this->api->getAlerts($siteIds);
        $this->assertCount(3, $alerts);
    }

    public function test_getAlerts_shouldReturnAllAlertsIfSuperUserAndReturnAllFlagIsEnabled()
    {
        $siteIds = array($this->idSite2, $this->idSite);

        $this->setSuperUser();
        FakeAccess::$identity    = 'AnyLogin';
        FakeAccess::$idSitesView = $siteIds;

        $alerts = $this->api->getAlerts($siteIds, true);
        $this->assertCount(3, $alerts);

        $alerts = $this->api->getAlerts($siteIds, false);
        $this->assertCount(0, $alerts);
    }

    public function test_getAlerts_shouldNotReturnAllAlertsIfReturnAllFlagIsEnabledButUserIsNotSuperUser()
    {
        $siteIds = array($this->idSite2, $this->idSite);

        $this->setUser();
        FakeAccess::$identity    = 'AnyLogin';
        FakeAccess::$idSitesView = $siteIds;

        $alerts = $this->api->getAlerts($siteIds, true);
        $this->assertCount(0, $alerts);
    }

    public function test_getAlerts_shouldFail_IfUserDoesNotHaveAccessToWebsite()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('checkUserHasViewAccess Fake exception');

        FakeAccess::clearAccess();

        $this->api->getAlerts(array($this->idSite));
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

    public function test_getAlert_ShouldFail_IfInvalidIdProvided()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CustomAlerts_AlertDoesNotExist');

        $this->setSuperUser();

        $this->api->getAlert(9999);
    }

    public function test_getAlert_ShouldFail_IfNotOwnerOfAlert()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CustomAlerts_AccessException');

        $this->api->getAlert(2);
    }

    public function test_getAlert_ShouldFail_IfNotOwnerOfAlertEventIfUserIsSuperUser()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CustomAlerts_AccessException');

        $this->setSuperUser();
        FakeAccess::$identity = 'test';
        $this->api->getAlert(2);
    }

    public function test_getAlert_ShouldReturnAlertIfAllowedAndExists()
    {
        $this->setSuperUser();
        $this->api->getAlert(2);

        $this->assertIsAlert(2, 'Initial2', 'week', array($this->idSite, $this->idSite2));
    }

    public function test_getValuesForAlertInPast_ShouldFail_IfInvalidIdProvided()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CustomAlerts_AlertDoesNotExist');

        $this->setSuperUser();

        $this->api->getValuesForAlertInPast(9999, 1);
    }

    public function test_getValuesForAlertInPast_ShouldFail_IfNotOwnerOfAlert()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CustomAlerts_AccessException');

        $this->api->getValuesForAlertInPast(2, 1);
    }

    public function test_getValuesForAlertInPast_ShouldFail_IfNotOwnerOfAlertEventIfUserIsSuperUser()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CustomAlerts_AccessException');

        $this->setSuperUser();
        FakeAccess::$identity = 'test';
        $this->api->getValuesForAlertInPast(2, 1);
    }

    public function test_getValuesForAlertInPast_ShouldReturnAValueForAllSites()
    {
        $this->setSuperUser();
        $values = $this->api->getValuesForAlertInPast(2, 0);

        $expected = array(
            array('idSite' => $this->idSite, 'value' => null),
            array('idSite' => $this->idSite2, 'value' => null),
        );

        $this->assertEquals($expected, $values);
    }

    public function test_deleteAlert_ShouldRemoveAlert()
    {
        $this->setSuperUser();
        $alerts    = $this->api->getAlerts(array($this->idSite, $this->idSite2));
        $numAlerts = count($alerts);

        $this->api->deleteAlert(2);

        $alerts         = $this->api->getAlerts(array($this->idSite, $this->idSite2));
        $numAlertsAfter = count($alerts);

        $this->assertEquals($numAlerts - 1, $numAlertsAfter);
    }

    public function test_deleteAlert_ShouldFail_IfNotOwnerOfAlert()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CustomAlerts_AccessException');

        $this->api->deleteAlert(2);
    }

    public function test_deleteAlert_ShouldFail_IfNotOwnerOfAlertEvenIfUserIsSuperuser()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CustomAlerts_AccessException');

        $this->setSuperUser();
        FakeAccess::$identity = 'test';

        $this->api->deleteAlert(2);
    }

    public function test_triggerAlert_getTriggeredAlertsForPeriod_ShouldMarkAlertAsTriggeredForGivenWebsite()
    {
        $this->setSuperUser();

        $this->model->triggerAlert(2, 1, 94, 48, Date::now()->getDatetime());
        $triggeredAlerts = $this->api->getTriggeredAlerts(array(1));

        $this->assertCount(1, $triggeredAlerts);

        $this->assertNotEmpty($triggeredAlerts[0]['ts_triggered']);
        unset($triggeredAlerts[0]['ts_triggered']);

        $expected = array(
            'idtriggered'       => 1,
            'idalert'           => 2,
            'idsite'            => 1,
            'ts_last_sent'      => null,
            'name'              => 'Initial2',
            'period'            => 'week',
            'login'             => 'superUserLogin',
            'report'            => 'MultiSites_getOne',
            'report_condition'  => 'matches_exactly',
            'report_matched'    => 'Piwik',
            'metric'            => 'nb_visits',
            'metric_condition'  => 'less_than',
            'metric_matched'    => '5',
            'value_new'         => 94,
            'value_old'         => 48,
            'additional_emails' => array('test1@example.com', 'test2@example.com'),
            'phone_numbers'     => array(),
            'email_me'          => 0,
            'compared_to'       => 1,
            'id_sites'          => array(1, 2)
        );

        $this->assertEquals(array($expected), $triggeredAlerts);
    }

    public function test_getTriggeredAlerts_ShouldThrowException_IfNotEnoughPermission()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('checkUserHasViewAccess Fake exception');

        $this->setUser();
        $this->api->getTriggeredAlerts(array(1));
    }

    public function test_getTriggeredAlerts_ShouldReturnAllThatMatchesLoginAndIdSite()
    {
        $idSite = 1;

        $this->setSuperUser();
        $this->model->triggerAlert(2, $idSite, 99, 48, Date::now()->getDatetime());

        $triggeredAlerts = $this->api->getTriggeredAlerts(array($idSite));
        $this->assertCount(1, $triggeredAlerts);

        $triggeredAlerts = $this->api->getTriggeredAlerts(array($idSite, 2));
        $this->assertCount(1, $triggeredAlerts);

        // no matching site
        $triggeredAlerts = $this->api->getTriggeredAlerts(array(2));
        $this->assertCount(0, $triggeredAlerts);

        // different login
        FakeAccess::$identity = 'differentLoginButStillSuperuser';
        $triggeredAlerts      = $this->api->getTriggeredAlerts(array($idSite, 2));
        $this->assertCount(0, $triggeredAlerts);

        // different login
        $this->setUser();
        FakeAccess::$idSitesView = array(1);
        $triggeredAlerts         = $this->api->getTriggeredAlerts(array(1));
        $this->assertCount(0, $triggeredAlerts);
    }

    protected function createAlert(
        $name,
        $period = 'week',
        $idSites = null,
        $metric = 'nb_visits',
        $report = 'MultiSites_getOne',
        $metricCondition = 'less_than',
        $reportCondition = 'matches_exactly',
        $emails = array('test1@example.com', 'test2@example.com'),
        $comparedTo = 1
    ) {
        if (is_null($idSites)) {
            $idSites = $this->idSite;
        }

        // those should be dropped by the api as they do not exist in Piwik
        $phoneNumbers = array('+1234567890', '1234567890');

        $id = $this->api->addAlert($name, $idSites, $period, 0, $emails, $phoneNumbers, $metric, $metricCondition,
            $metricMatched = 5, $comparedTo, $report, $reportCondition, 'Piwik');
        return $id;
    }

    protected function editAlert(
        $id,
        $name,
        $period = 'week',
        $idSites = null,
        $metric = 'nb_visits',
        $report = 'MultiSites_getOne',
        $metricCondition = 'less_than',
        $reportCondition = 'matches_exactly',
        $emails = array('test1@example.com', 'test2@example.com')
    ) {
        if (is_null($idSites)) {
            $idSites = $this->idSite;
        }

        // those should be dropped by the api as they do not exist in Piwik
        $phoneNumbers = array('+1234567890', '1234567890');
        $comparedTo   = 1;

        $id = $this->api->editAlert($id, $name, $idSites, $period, 0, $emails, $phoneNumbers, $metric, $metricCondition,
            $metricMatched = 5, $comparedTo, $report, $reportCondition, 'Piwik');
        return $id;
    }

    protected function assertIsAlert(
        $id,
        $name,
        $period = 'week',
        $idSites = null,
        $login = 'superUserLogin',
        $metric = 'nb_visits',
        $metricCondition = 'less_than',
        $metricMatched = 5,
        $report = 'MultiSites_getOne',
        $reportCondition = 'matches_exactly',
        $reportMatched = 'Piwik'
    ) {
        if (is_null($idSites)) {
            $idSites = array($this->idSite);
        }

        $alert = $this->api->getAlert($id);

        $expected = array(
            'idalert'           => $id,
            'name'              => $name,
            'login'             => $login,
            'period'            => $period,
            'report'            => $report,
            'report_condition'  => $reportCondition,
            'report_matched'    => $reportMatched,
            'metric'            => $metric,
            'metric_condition'  => $metricCondition,
            'metric_matched'    => $metricMatched,
            'email_me'          => 0,
            'additional_emails' => array('test1@example.com', 'test2@example.com'),
            'phone_numbers'     => array(),
            'compared_to'       => 1,
            'id_sites'          => $idSites
        );

        $this->assertEquals($expected, $alert);
    }
}