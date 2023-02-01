<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\CustomAlerts\tests\Integration;

use Piwik\Cache as PiwikCache;
use Piwik\Container\StaticContainer;
use Piwik\Plugin;
use Piwik\Plugins\CustomAlerts\Controller;
use Piwik\SettingsPiwik;
use Piwik\Tests\Framework\Fixture;
use Piwik\Version;

class CustomController extends Controller
{
    public function __construct()
    {
        parent::__construct(StaticContainer::get('Piwik\Plugins\API\ProcessedReport'));
    }

    public function enrichTriggeredAlerts($triggeredAlerts)
    {
        return parent::enrichTriggeredAlerts($triggeredAlerts);
    }
}

/**
 * @group CustomAlerts
 * @group ControllerTest
 * @group Plugins
 */
class ControllerTest extends BaseTest
{
    /**
     * @var CustomController
     */
    private $controller;

    public function setUp(): void
    {
        parent::setUp();

        // make sure templates will be found
        Plugin\Manager::getInstance()->loadPlugin('CustomAlerts');
        Plugin\Manager::getInstance()->loadPlugin('Morpheus');

        PiwikCache::flushAll();

        Fixture::loadAllTranslations();

        $this->controller = new CustomController();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Fixture::resetTranslations();
    }

    public function test_formatAlerts_asText()
    {
        $alerts = $this->getTriggeredAlerts();

        $host = SettingsPiwik::getPiwikUrl();

        $expected = <<<FORMATTED
MyName1 has been triggered as the metric Visits in report Single Website dashboard decreased more than 5000 from 228.128 to 4493.
>> Edit Alert ${host}index.php?module=CustomAlerts&action=editAlert&idAlert=1&idSite=1&period=week&date=yesterday

MyName2 has been triggered as the metric Visits in report Single Website dashboard decreased more than 5000 from 228.128 to 4493.
>> Edit Alert ${host}index.php?module=CustomAlerts&action=editAlert&idAlert=2&idSite=1&period=week&date=yesterday


FORMATTED;

        $rendered = $this->controller->formatAlerts($alerts, 'text');

        $this->assertEquals($expected, $rendered);
    }

    /**
     * @return array
     */
    private function getTriggeredAlerts()
    {
        return array(
            $this->buildAlert(1, 'MyName1'),
            $this->buildAlert(2, 'MyName2'),
        );
    }

    private function buildAlert(
        $id,
        $name,
        $period = 'week',
        $idSite = 1,
        $siteName = 'Piwik test',
        $login = 'superUserLogin',
        $metric = 'nb_visits',
        $metricCondition = 'decrease_more_than',
        $metricMatched = 5000,
        $report = 'MultiSites_getOne',
        $reportCondition = 'matches_exactly',
        $reportMatched = 'Piwik'
    )
    {
        return array(
            'idalert'           => $id,
            'idsite'            => $idSite,
            'name'              => $name,
            'period'            => $period,
            'login'             => $login,
            'report'            => $report,
            'report_condition'  => $reportCondition,
            'report_matched'    => $reportMatched,
            'metric'            => $metric,
            'metric_condition'  => $metricCondition,
            'metric_matched'    => $metricMatched,
            'additional_emails' => array('test5@example.com'),
            'phone_numbers'     => array('+1234567890', '232'),
            'email_me'          => 1,
            'value_new'         => '4493.000',
            'value_old'         => '228.128',
            'ts_triggered'      => time()
        );
    }

    public function test_formatAlerts_asSms()
    {
        $alerts = $this->getTriggeredAlerts();

        $expected = <<<FORMATTED
MyName1 has been triggered for website Piwik test as the metric Visits in report Single Website dashboard decreased more than 5000 from 228.128 to 4493. MyName2 has been triggered for website Piwik test as the metric Visits in report Single Website dashboard decreased more than 5000 from 228.128 to 4493.
FORMATTED;

        $rendered = $this->controller->formatAlerts($alerts, 'sms');

        $this->assertEquals($expected, $rendered);
    }

    public function test_formatAlerts_ShouldThrowException_IfInvalidFormatGiven()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unsupported format');

        $alerts = $this->getTriggeredAlerts();
        $this->controller->formatAlerts($alerts, 'php');
    }

    public function test_formatAlerts_asHtml()
    {
        $alerts = $this->getTriggeredAlerts();

        $host = SettingsPiwik::getPiwikUrl();

        $rendered = $this->controller->formatAlerts($alerts, 'html');

        $expected = <<<FORMATTED
<table style='width:100%;border-collapse: collapse; border:1px solid rgb(231,231,231); padding:5px;  margin:30px 0;color:#0d0d0d;font-family:-apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Oxygen-Sans, Cantarell, &quot;Helvetica Neue&quot;, sans-serif; ' class="tableForm dataTable card entityTable card-table">
    <thead style="background-color:#f2f2f2;color:#0d0d0d;">
    <tr>
        <th style="padding:13px 0 13px 10px;text-align: left;font-weight:normal;font-size: 15px;color:#0d0d0d;font-family:-apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Oxygen-Sans, Cantarell, &quot;Helvetica Neue&quot;, sans-serif; ">Alert Name</th>
                <th style="padding:13px 0 13px 10px;text-align: left;font-weight:normal;font-size: 15px;color:#0d0d0d;font-family:-apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Oxygen-Sans, Cantarell, &quot;Helvetica Neue&quot;, sans-serif; ">Report</th>
        <th style="padding:13px 0 13px 10px;text-align: left;font-weight:normal;font-size: 15px;color:#0d0d0d;font-family:-apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Oxygen-Sans, Cantarell, &quot;Helvetica Neue&quot;, sans-serif; ">Alert Condition</th>
        <th style="padding:13px 0 13px 10px;text-align: left;font-weight:normal;font-size: 15px;color:#0d0d0d;font-family:-apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Oxygen-Sans, Cantarell, &quot;Helvetica Neue&quot;, sans-serif; ">Alert</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style="border-bottom:1px solid rgb(231,231,231);font-size: 15px;color:#0d0d0d;font-family:-apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Oxygen-Sans, Cantarell, &quot;Helvetica Neue&quot;, sans-serif; padding:17px 0 17px 10px;"><a style="color:#439fe0; text-decoration:none;" href="${host}index.php?module=CustomAlerts&action=editAlert&idAlert=1&idSite=1&period=week&date=yesterday">MyName1</a></td>
                <td style="border-bottom:1px solid rgb(231,231,231);font-size: 15px;color:#0d0d0d;font-family:-apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Oxygen-Sans, Cantarell, &quot;Helvetica Neue&quot;, sans-serif; padding:17px 0 17px 10px;">Single Website dashboard</td>
        <td style="border-bottom:1px solid rgb(231,231,231);font-size: 15px;color:#0d0d0d;font-family:-apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Oxygen-Sans, Cantarell, &quot;Helvetica Neue&quot;, sans-serif; padding:17px 0 17px 10px;">Website is 'Piwik'</td>
        <td style="border-bottom:1px solid rgb(231,231,231);font-size: 15px;color:#0d0d0d;font-family:-apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Oxygen-Sans, Cantarell, &quot;Helvetica Neue&quot;, sans-serif; padding:17px 0 17px 10px;padding:17px 10px;">Visits decreased more than 5000 from 228.128 to 4493</td>
    </tr>

    <tr>
        <td style="border-bottom:1px solid rgb(231,231,231);font-size: 15px;color:#0d0d0d;font-family:-apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Oxygen-Sans, Cantarell, &quot;Helvetica Neue&quot;, sans-serif; padding:17px 0 17px 10px;"><a style="color:#439fe0; text-decoration:none;" href="${host}index.php?module=CustomAlerts&action=editAlert&idAlert=2&idSite=1&period=week&date=yesterday">MyName2</a></td>
                <td style="border-bottom:1px solid rgb(231,231,231);font-size: 15px;color:#0d0d0d;font-family:-apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Oxygen-Sans, Cantarell, &quot;Helvetica Neue&quot;, sans-serif; padding:17px 0 17px 10px;">Single Website dashboard</td>
        <td style="border-bottom:1px solid rgb(231,231,231);font-size: 15px;color:#0d0d0d;font-family:-apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Oxygen-Sans, Cantarell, &quot;Helvetica Neue&quot;, sans-serif; padding:17px 0 17px 10px;">Website is 'Piwik'</td>
        <td style="border-bottom:1px solid rgb(231,231,231);font-size: 15px;color:#0d0d0d;font-family:-apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Oxygen-Sans, Cantarell, &quot;Helvetica Neue&quot;, sans-serif; padding:17px 0 17px 10px;padding:17px 10px;">Visits decreased more than 5000 from 228.128 to 4493</td>
    </tr>

    </tbody>
</table>

FORMATTED;

        $this->assertEquals($expected, $rendered, "Got following HTML response: " . var_export($rendered, true));

    }

    public function test_formatAlertsNoConditions_asHtml()
    {
        $alerts = array(
            $this->buildAlert(1, 'My Alert', 'week', 1, 'Piwik Site', 'superUserLogin', 'nb_visits', 'decrease_more_than', 5000, 'MultiSites_getOne', null, null)
        );

        $host = SettingsPiwik::getPiwikUrl();

        $rendered = $this->controller->formatAlerts($alerts, 'html');

        $expected = <<<FORMATTED
<table style='width:100%;border-collapse: collapse; border:1px solid rgb(231,231,231); padding:5px;  margin:30px 0;color:#0d0d0d;font-family:-apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Oxygen-Sans, Cantarell, &quot;Helvetica Neue&quot;, sans-serif; ' class="tableForm dataTable card entityTable card-table">
    <thead style="background-color:#f2f2f2;color:#0d0d0d;">
    <tr>
        <th style="padding:13px 0 13px 10px;text-align: left;font-weight:normal;font-size: 15px;color:#0d0d0d;font-family:-apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Oxygen-Sans, Cantarell, &quot;Helvetica Neue&quot;, sans-serif; ">Alert Name</th>
                <th style="padding:13px 0 13px 10px;text-align: left;font-weight:normal;font-size: 15px;color:#0d0d0d;font-family:-apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Oxygen-Sans, Cantarell, &quot;Helvetica Neue&quot;, sans-serif; ">Report</th>
        <th style="padding:13px 0 13px 10px;text-align: left;font-weight:normal;font-size: 15px;color:#0d0d0d;font-family:-apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Oxygen-Sans, Cantarell, &quot;Helvetica Neue&quot;, sans-serif; ">Alert</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style="border-bottom:1px solid rgb(231,231,231);font-size: 15px;color:#0d0d0d;font-family:-apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Oxygen-Sans, Cantarell, &quot;Helvetica Neue&quot;, sans-serif; padding:17px 0 17px 10px;"><a style="color:#439fe0; text-decoration:none;" href="${host}index.php?module=CustomAlerts&action=editAlert&idAlert=1&idSite=1&period=week&date=yesterday">My Alert</a></td>
                <td style="border-bottom:1px solid rgb(231,231,231);font-size: 15px;color:#0d0d0d;font-family:-apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Oxygen-Sans, Cantarell, &quot;Helvetica Neue&quot;, sans-serif; padding:17px 0 17px 10px;">Single Website dashboard</td>
        <td style="border-bottom:1px solid rgb(231,231,231);font-size: 15px;color:#0d0d0d;font-family:-apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Oxygen-Sans, Cantarell, &quot;Helvetica Neue&quot;, sans-serif; padding:17px 0 17px 10px;padding:17px 10px;">Visits decreased more than 5000 from 228.128 to 4493</td>
    </tr>

    </tbody>
</table>

FORMATTED;

        $this->assertEquals($expected, $rendered, "Got following HTML response: " . var_export($rendered, true));

    }

    public function test_enrichTriggeredAlerts_shouldEnrichAlerts_IfReportExistsAndMetricIsValid()
    {
        $timestamp = 1389824417;
        $alerts    = array(
            array(
                'idsite'           => 1,
                'period'           => 'day',
                'ts_triggered'     => $timestamp,
                'metric'           => 'nb_visits',
                'report'           => 'MultiSites_getAll',
                'report_condition' => 'matches_any',
                'value_old'        => '228.001',
                'value_new'        => '1.0'
            ),
            array(
                'idsite'           => 1,
                'period'           => 'week',
                'ts_triggered'     => $timestamp,
                'metric'           => 'nb_visits',
                'report'           => 'NotExistingModule_Action',
                'report_condition' => 'matches_exactly',
                'value_old'        => '228.000',
                'value_new'        => '1.0'
            ),
            array(
                'idsite'           => 1,
                'period'           => 'month',
                'ts_triggered'     => $timestamp,
                'metric'           => 'bounce_rate',
                'report'           => 'Actions_getPageUrls',
                'report_condition' => 'matches_exactly',
                'value_old'        => '228.999',
                'value_new'        => '1.0'
            ),
            array(
                'idsite'           => 1,
                'period'           => 'day',
                'ts_triggered'     => $timestamp,
                'metric'           => 'not_valid',
                'report'           => 'Actions_getPageUrls',
                'report_condition' => 'contains',
                'value_old'        => '228.001',
                'value_new'        => '1.01'
            ),
            // no dimension ,
            array(
                'idsite'           => 1,
                'period'           => 'day',
                'ts_triggered'     => $timestamp,
                'metric'           => 'nb_visits',
                'report'           => 'VisitsSummary_get',
                'report_condition' => 'matches_any',
                'value_old'        => '228.001',
                'value_new'        => '10'
            )
        );

        $enriched = $this->controller->enrichTriggeredAlerts($alerts);


        $alerts[0]['reportName']          = 'All Websites dashboard';
        $alerts[0]['reportMetric']        = 'Visits';
        $alerts[0]['dimension']           = 'Website';
        $alerts[0]['reportConditionName'] = 'matches any';
        $alerts[0]['value_old']           = '228.001';
        $alerts[0]['value_new']           = 1;
        $alerts[0]['siteName']            = 'Piwik test';
        $alerts[0]['ts_triggered']        = 'Tue, Jan 14';
        self::assertIsInt($alerts[0]['value_new']);
        $alerts[1]['reportName']          = null;
        $alerts[1]['reportMetric']        = null;
        $alerts[1]['dimension']           = null;
        $alerts[1]['reportConditionName'] = null;
        $alerts[1]['value_old']           = 228;
        $alerts[1]['value_new']           = 1;
        $alerts[1]['siteName']            = 'Piwik test';
        $alerts[1]['ts_triggered']        = 'Jan 6 – 12, 2014';
        if (version_compare(Version::VERSION, '4.13.0-rc1', '<')) {
            // intl format changed in 4.13.0
            $alerts[1]['ts_triggered'] = 'Jan 6 – 12, 2014';
        }
        self::assertIsInt($alerts[1]['value_old']);
        self::assertIsInt($alerts[1]['value_new']);
        $alerts[2]['reportName']          = 'Page URLs';
        $alerts[2]['reportMetric']        = 'Bounce Rate';
        $alerts[2]['dimension']           = 'Page URL';
        $alerts[2]['reportConditionName'] = 'is';
        $alerts[2]['value_old']           = '228.999';
        $alerts[2]['value_new']           = '1';
        $alerts[2]['siteName']            = 'Piwik test';
        $alerts[2]['ts_triggered']        = 'Dec 2013';
        $alerts[3]['reportName']          = 'Page URLs';
        $alerts[3]['reportMetric']        = null;
        $alerts[3]['dimension']           = 'Page URL';
        $alerts[3]['reportConditionName'] = 'contains';
        $alerts[3]['value_old']           = '228.001';
        $alerts[3]['value_new']           = '1.01';
        $alerts[3]['siteName']            = 'Piwik test';
        $alerts[3]['ts_triggered']        = 'Tue, Jan 14';
        $alerts[4]['reportName']          = 'Visits Summary';
        $alerts[4]['reportMetric']        = 'Visits';
        $alerts[4]['dimension']           = null;
        $alerts[4]['reportConditionName'] = 'matches any';
        $alerts[4]['value_old']           = '228.001';
        $alerts[4]['value_new']           = 10;
        $alerts[4]['siteName']            = 'Piwik test';
        $alerts[4]['ts_triggered']        = 'Tue, Jan 14';
        self::assertIsInt($alerts[4]['value_new']);

        $this->assertEquals($alerts, $enriched);
    }

}
