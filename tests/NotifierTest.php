<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\CustomAlerts\tests;
use Piwik\Plugin;
use Piwik\Plugins\CustomAlerts\Notifier;

/**
 * @group CustomAlerts
 * @group NotifierTest
 * @group Unit
 */
class NotifierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Notifier
     */
    private $notifier;

    public function setUp()
    {
        parent::setUp();

        // make sure templates will be found
        Plugin\Manager::getInstance()->loadPlugin('CustomAlerts');
        Plugin\Manager::getInstance()->loadPlugin('Zeitgeist');

        $this->notifier = new Notifier();
    }

    public function test_formatAlerts_asTsv()
    {
        $alerts = array(
            $this->buildAlert(1, 'MyName1'),
            $this->buildAlert(2, 'MyName2'),
        );

        $expected = <<<FORMATTED
idalert	idsite	alert_name	period	site_name	login	report	report_condition	report_matched	metric	metric_condition	metric_matched
1	1	MyName1	week	Piwik test	superUserLogin	MultiSites.getOne	matches_exactly	Piwik	nb_visits	less_than	5
2	1	MyName2	week	Piwik test	superUserLogin	MultiSites.getOne	matches_exactly	Piwik	nb_visits	less_than	5

FORMATTED;

        $rendered = $this->notifier->formatAlerts($alerts, 'tsv');

        $this->assertEquals($expected, $rendered);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unsupported format
     */
    public function test_formatAlerts_ShouldThrowException_IfInvalidFormatGiven()
    {
        $alerts = array(
            $this->buildAlert(1, 'MyName1')
        );

        $rendered = $this->notifier->formatAlerts($alerts, 'php');

        $this->assertEquals($alerts, $rendered);
    }

    public function test_formatAlerts_asHtml()
    {
        $alerts = array(
            $this->buildAlert(1, 'MyName1'),
            $this->buildAlert(2, 'MyName2'),
        );

        $rendered = $this->notifier->formatAlerts($alerts, 'html');

        $expected = <<<FORMATTED
<table>
            <thead>
        <tr bgcolor='#c0c0c0'>
                        <td>idalert</td>
                        <td>idsite</td>
                        <td>alert_name</td>
                        <td>period</td>
                        <td>site_name</td>
                        <td>login</td>
                        <td>report</td>
                        <td>report_condition</td>
                        <td>report_matched</td>
                        <td>metric</td>
                        <td>metric_condition</td>
                        <td>metric_matched</td>
                    </tr>
        </thead>
        <tbody>
        <tr>
                <td>1</td>
                <td>1</td>
                <td>MyName1</td>
                <td>week</td>
                <td>Piwik test</td>
                <td>superUserLogin</td>
                <td>MultiSites.getOne</td>
                <td>matches_exactly</td>
                <td>Piwik</td>
                <td>nb_visits</td>
                <td>less_than</td>
                <td>5</td>
            </tr>
            </tbody>
            <tr>
                <td>2</td>
                <td>1</td>
                <td>MyName2</td>
                <td>week</td>
                <td>Piwik test</td>
                <td>superUserLogin</td>
                <td>MultiSites.getOne</td>
                <td>matches_exactly</td>
                <td>Piwik</td>
                <td>nb_visits</td>
                <td>less_than</td>
                <td>5</td>
            </tr>
    </table>
FORMATTED;

        $this->assertEquals($expected, $rendered);
    }

    private function buildAlert($id, $name, $period = 'week', $idSite = 1, $siteName = 'Piwik test', $login = 'superUserLogin', $metric = 'nb_visits', $metricCondition = 'less_than', $metricMatched = 5, $report = 'MultiSites.getOne', $reportCondition = 'matches_exactly', $reportMatched = 'Piwik')
    {
        return array(
            'idalert' => $id,
            'idsite' => $idSite,
            'alert_name' => $name,
            'period' => $period,
            'site_name' => $siteName,
            'login' => $login,
            'report' => $report,
            'report_condition' => $reportCondition,
            'report_matched' => $reportMatched,
            'metric' => $metric,
            'metric_condition' => $metricCondition,
            'metric_matched' => $metricMatched
        );
    }
}