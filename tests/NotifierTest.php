<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\CustomAlerts\tests;

use Piwik\Access;
use Piwik\Mail;
use Piwik\Plugin;
use Piwik\Plugins\CustomAlerts\Notifier;
use Piwik\Translate;

class CustomNotifier extends Notifier
{
    private $alerts = array();

    protected function getTriggeredAlerts($period)
    {
        return $this->alerts;
    }

    public function setTriggeredAlerts($alerts)
    {
        $this->alerts = $alerts;
    }

    public function formatAlerts($triggeredAlerts, $format) {
        return parent::formatAlerts($triggeredAlerts, $format);
    }

    public function enrichTriggeredAlerts($triggeredAlerts) {
        return parent::enrichTriggeredAlerts($triggeredAlerts);
    }

    public function sendAlertsPerEmailToRecipient($alerts, \Piwik\Mail $mail, $recipient)
    {
        parent::sendAlertsPerEmailToRecipient($alerts, $mail, $recipient);
    }
}

/**
 * @group CustomAlerts
 * @group NotifierTest
 * @group Database
 */
class NotifierTest extends \DatabaseTestCase
{
    /**
     * @var CustomNotifier
     */
    private $notifier;

    public function setUp()
    {
        parent::setUp();

        $this->setSuperUser();

        // make sure templates will be found
        Plugin\Manager::getInstance()->loadPlugin('CustomAlerts');
        Plugin\Manager::getInstance()->loadPlugin('Zeitgeist');

        Translate::reloadLanguage('en');

        \Test_Piwik_BaseFixture::createWebsite('2012-08-09 11:22:33');
        \Piwik\Plugins\UsersManager\API::getInstance()->addUser('login1', 'p2kK2msAw1', 'test1@example.com');
        \Piwik\Plugins\UsersManager\API::getInstance()->addUser('login2', 'p2kK2msAw1', 'test2@example.com');
        \Piwik\Plugins\UsersManager\API::getInstance()->addUser('login3', 'p2kK2msAw1', 'test3@example.com');

        $this->notifier = new CustomNotifier();
    }

    public function test_formatAlerts_asText()
    {
        $alerts = $this->getTriggeredAlerts();

        $expected = <<<FORMATTED
MyName1 has been triggered as the metric Visits in report Single Website dashboard has changed for website Piwik test from 228 to 4493.
>> Edit Alert http://apache.piwik/index.php?module=CustomAlerts&action=editAlert&idAlert=1&idSite=1&period=week&date=yesterday

MyName2 has been triggered as the metric Visits in report Single Website dashboard has changed for website Piwik test from 228 to 4493.
>> Edit Alert http://apache.piwik/index.php?module=CustomAlerts&action=editAlert&idAlert=2&idSite=1&period=week&date=yesterday


FORMATTED;

        $rendered = $this->notifier->formatAlerts($alerts, 'text');

        $this->assertEquals($expected, $rendered);
    }

    public function test_formatAlerts_asSms()
    {
        $alerts = $this->getTriggeredAlerts();

        $expected = <<<FORMATTED
MyName1 has been triggered as the metric Visits in report Single Website dashboard has changed for website Piwik test from 228 to 4493. MyName2 has been triggered as the metric Visits in report Single Website dashboard has changed for website Piwik test from 228 to 4493.
FORMATTED;

        $rendered = $this->notifier->formatAlerts($alerts, 'sms');

        $this->assertEquals($expected, $rendered);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unsupported format
     */
    public function test_formatAlerts_ShouldThrowException_IfInvalidFormatGiven()
    {
        $alerts = $this->getTriggeredAlerts();
        $this->notifier->formatAlerts($alerts, 'php');
    }

    public function test_formatAlerts_asHtml()
    {
        $alerts = $this->getTriggeredAlerts();

        $rendered = $this->notifier->formatAlerts($alerts, 'html');

        $expected = <<<FORMATTED
<table>
    <thead>
    <tr bgcolor='#c0c0c0'>
        <td>Alert</td>
        <td>Edit</td>
    </tr>
    </thead>
    <tbody>

    <tr>
        <td>MyName1 has been triggered as the metric Visits in report Single Website dashboard has changed for website Piwik test from 228 to 4493.</td>
        <td><a href="http://apache.piwik/index.php?module=CustomAlerts&action=editAlert&idAlert=1&idSite=1&period=week&date=yesterday"
                >Edit Alert</a></td>
    </tr>


    <tr>
        <td>MyName2 has been triggered as the metric Visits in report Single Website dashboard has changed for website Piwik test from 228 to 4493.</td>
        <td><a href="http://apache.piwik/index.php?module=CustomAlerts&action=editAlert&idAlert=2&idSite=1&period=week&date=yesterday"
                >Edit Alert</a></td>
    </tr>

    </tbody>
</table>
FORMATTED;

        $this->assertEquals($expected, $rendered);
    }

    public function test_sendAlertsPerEmailToRecipient()
    {
        $alerts = $this->getTriggeredAlerts();
        $mail   = new Mail();
        Mail::setDefaultTransport(new \Zend_Mail_Transport_File());

        $this->notifier->sendAlertsPerEmailToRecipient($alerts, $mail, 'test@example.com');

        $expectedHtml = <<<HTML
Dear Piwik User,<br /><br />=0A=0AThe custom alerts you requested from P=
iwik Alerts are listed in the table below. To see more details or to adj=
ust your custom alert settings, please sign in to your Piwik Open Source=
 Analytics account and access the Alerts page.<br /><br />=0A=0A<table>=
=0A    <thead>=0A    <tr bgcolor=3D'#c0c0c0'>=0A        <td>Alert</td>=
=0A        <td>Edit</td>=0A    </tr>=0A    </thead>=0A    <tbody>=0A=0A=
    <tr>=0A        <td>MyName1 has been triggered as the metric Visits i=
n report Single Website dashboard has changed for website Piwik test fro=
m 228 to 4493.</td>=0A        <td><a href=3D"http://apache.piwik/index.p=
hp?module=3DCustomAlerts&action=3DeditAlert&idAlert=3D1&idSite=3D1&perio=
d=3Dweek&date=3Dyesterday"=0A                >Edit Alert</a></td>=0A   =
 </tr>=0A=0A=0A    <tr>=0A        <td>MyName2 has been triggered as the=
 metric Visits in report Single Website dashboard has changed for websit=
e Piwik test from 228 to 4493.</td>=0A        <td><a href=3D"http://apac=
he.piwik/index.php?module=3DCustomAlerts&action=3DeditAlert&idAlert=3D2&=
idSite=3D1&period=3Dweek&date=3Dyesterday"=0A                >Edit Alert=
</a></td>=0A    </tr>=0A=0A    </tbody>=0A</table>=0A<br />=0AHappy anal=
yzing!
HTML;

        $expectedText = 'Dear Piwik User,=0A=0AThe custom alerts you requested from Piwik Alerts=
 are listed in the table below. To see more details or to adjust your cu=
stom alert settings, please sign in to your Piwik Open Source Analytics=
 account and access the Alerts page.=0A=0AMyName1 has been triggered as=
 the metric Visits in report Single Website dashboard has changed for we=
bsite Piwik test from 228 to 4493.=0A>> Edit Alert http://apache.piwik/i=
ndex.php?module=3DCustomAlerts&action=3DeditAlert&idAlert=3D1&idSite=3D1=
&period=3Dweek&date=3Dyesterday=0A=0AMyName2 has been triggered as the m=
etric Visits in report Single Website dashboard has changed for website=
 Piwik test from 228 to 4493.=0A>> Edit Alert http://apache.piwik/index.=
php?module=3DCustomAlerts&action=3DeditAlert&idAlert=3D2&idSite=3D1&peri=
od=3Dweek&date=3Dyesterday=0A=0A=0A=0AHappy analyzing!';

        $this->assertEquals($expectedHtml, html_entity_decode($mail->getBodyHtml(true)));
        $this->assertEquals($expectedText, $mail->getBodyText(true));
        $this->assertEquals(array('test@example.com'), $mail->getRecipients());
    }

    public function test_sendNewAlerts()
    {
        $mock = $this->getMock('Piwik\Plugins\CustomAlerts\tests\CustomNotifier', array('sendAlertsPerEmailToRecipient', 'sendAlertsPerSmsToRecipient'));
        $alerts = array(
            $this->buildAlert(1, 'Alert1', 'week', 4, 'Test', 'login1'),
            $this->buildAlert(2, 'Alert2', 'week', 4, 'Test', 'login2'),
            $this->buildAlert(3, 'Alert3', 'week', 4, 'Test', 'login1'),
            $this->buildAlert(4, 'Alert4', 'week', 4, 'Test', 'login3'),
        );

        $mock->setTriggeredAlerts($alerts);

        $mock->expects($this->at(0))
            ->method('sendAlertsPerEmailToRecipient')
            ->with($this->equalTo($alerts),
                $this->isInstanceOf('\Piwik\Mail'),
                $this->equalTo('test5@example.com'));

        $mock->expects($this->at(1))
             ->method('sendAlertsPerEmailToRecipient')
             ->with($this->equalTo(array($alerts[0], $alerts[2])),
                    $this->isInstanceOf('\Piwik\Mail'),
                    $this->equalTo('test1@example.com'));

        $mock->expects($this->at(2))
             ->method('sendAlertsPerEmailToRecipient')
            ->with($this->equalTo(array($alerts[1])),
                   $this->isInstanceOf('\Piwik\Mail'),
                   $this->equalTo('test2@example.com'));

        $mock->expects($this->at(3))
             ->method('sendAlertsPerEmailToRecipient')
             ->with($this->equalTo(array($alerts[3])),
                    $this->isInstanceOf('\Piwik\Mail'),
                    $this->equalTo('test3@example.com'));

        $mock->sendNewAlerts('week');
    }

    public function test_enrichTriggeredAlerts_shouldEnrichAlerts_IfReportExists()
    {
        $alerts = array(
            array('idsite' => 1, 'metric' => 'nb_visits', 'report' => 'MultiSites.getAll'),
            array('idsite' => 1, 'metric' => 'nb_visits', 'report' => 'NotExistingModule.Action'),
            array('idsite' => 1, 'metric' => 'bounce_rate', 'report' => 'Actions.getPageUrls')
        );

        $enriched = $this->notifier->enrichTriggeredAlerts($alerts);

        $alerts[0]['reportName']   = 'All Websites dashboard';
        $alerts[0]['reportMetric'] = 'Visits';
        $alerts[2]['reportName']   = 'Page URLs';
        $alerts[2]['reportMetric'] = 'Bounce Rate';

        $this->assertEquals($alerts, $enriched);
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
            'metric_matched' => $metricMatched,
            'additional_emails' => array('test5@example.com'),
            'phone_numbers' => array(),
            'email_me' => true,
            'value_new' => '4493',
            'value_old' => '228'
        );
    }

    /**
     * @return array
     */
    private function getTriggeredAlerts()
    {
        $alerts = array(
            $this->buildAlert(1, 'MyName1'),
            $this->buildAlert(2, 'MyName2'),
        );
        return $alerts;
    }

    private function setSuperUser()
    {
        $pseudoMockAccess = new \FakeAccess();
        \FakeAccess::$superUser = true;
        Access::setSingletonInstance($pseudoMockAccess);
    }

}