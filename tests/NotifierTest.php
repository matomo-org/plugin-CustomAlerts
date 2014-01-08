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
use Piwik\Mail;
use Piwik\Plugin;
use Piwik\Plugins\CustomAlerts\Notifier;
use Piwik\Translate;
use Piwik\Url;

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

    public function sendAlertsPerSmsToRecipient($alerts, $mobileMessagingAPI, $phoneNumber)
    {
        parent::sendAlertsPerSmsToRecipient($alerts, $mobileMessagingAPI, $phoneNumber);
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

        $host = Common::sanitizeInputValue(Url::getCurrentUrlWithoutFileName());

        $expected = <<<FORMATTED
MyName1 has been triggered for website Piwik test as the metric Visits in report Single Website dashboard is 4493 which is less than 5000.
>> Edit Alert ${host}index.php?module=CustomAlerts&action=editAlert&idAlert=1&idSite=1&period=week&date=yesterday

MyName2 has been triggered for website Piwik test as the metric Visits in report Single Website dashboard is 4493 which is less than 5000.
>> Edit Alert ${host}index.php?module=CustomAlerts&action=editAlert&idAlert=2&idSite=1&period=week&date=yesterday


FORMATTED;

        $rendered = $this->notifier->formatAlerts($alerts, 'text');

        $this->assertEquals($expected, $rendered);
    }

    public function test_formatAlerts_asSms()
    {
        $alerts = $this->getTriggeredAlerts();

        $expected = <<<FORMATTED
MyName1 has been triggered for website Piwik test as the metric Visits in report Single Website dashboard is 4493 which is less than 5000. MyName2 has been triggered for website Piwik test as the metric Visits in report Single Website dashboard is 4493 which is less than 5000.
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

        $host = Common::sanitizeInputValue(Url::getCurrentUrlWithoutFileName());

        $rendered = $this->notifier->formatAlerts($alerts, 'html');

        $expected = <<<FORMATTED
<table style="border-collapse: collapse;margin-left: 5px;">
    <thead style="background-color:rgb(228,226,215);color:rgb(37,87,146);">
    <tr>
        <th style="padding:6px 6px;text-align: left;">Alert</th>
        <th style="padding:6px 6px;text-align: left;width: 80px;" width="80">Edit</th>
    </tr>
    </thead>
    <tbody>

    <tr>
        <td style="border-bottom:1px solid rgb(231,231,231);padding:5px 0 5px 6px;">&#039;MyName1&#039; has been triggered for website Piwik test as the metric Visits in report Single Website dashboard is 4493 which is less than 5000.</td>
        <td style="border-bottom:1px solid rgb(231,231,231);padding:5px 0 5px 6px;"><a href="${host}index.php?module=CustomAlerts&action=editAlert&idAlert=1&idSite=1&period=week&date=yesterday"
                >Edit Alert</a></td>
    </tr>


    <tr>
        <td style="border-bottom:1px solid rgb(231,231,231);padding:5px 0 5px 6px;">&#039;MyName2&#039; has been triggered for website Piwik test as the metric Visits in report Single Website dashboard is 4493 which is less than 5000.</td>
        <td style="border-bottom:1px solid rgb(231,231,231);padding:5px 0 5px 6px;"><a href="${host}index.php?module=CustomAlerts&action=editAlert&idAlert=2&idSite=1&period=week&date=yesterday"
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
Hello,<br /><br />=0A=0AThe custom alerts you requested are listed in th=
e table below. To see more details or to adjust your custom alert settin=
gs, please sign in and access the Alerts page.<br /><br />=0A=0A<table
HTML;

        $expectedText = 'Hello,=0A=0AThe custom alerts you requested are listed in the table belo=
w. To see more details or to adjust your custom alert settings, please s=
ign in and access the Alerts page.=0A=0A';

        $this->assertStringStartsWith($expectedHtml, html_entity_decode($mail->getBodyHtml(true)));
        $this->assertStringStartsWith($expectedText, $mail->getBodyText(true));
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

        $alerts[2]['phone_numbers'] = array('232');

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

        $mock->expects($this->at(4))
             ->method('sendAlertsPerSmsToRecipient')
             ->with($this->equalTo(array($alerts[0], $alerts[1], $alerts[3])),
                    $this->isInstanceOf('\Piwik\Plugins\MobileMessaging\API'),
                    $this->equalTo('+1234567890'));

        $mock->expects($this->at(5))
             ->method('sendAlertsPerSmsToRecipient')
             ->with($this->equalTo($alerts),
                    $this->isInstanceOf('\Piwik\Plugins\MobileMessaging\API'),
                    $this->equalTo('232'));

        $mock->sendNewAlerts('week');
    }

    public function test_enrichTriggeredAlerts_shouldEnrichAlerts_IfReportExistsAndMetricIsValid()
    {
        $alerts = array(
            array('idsite' => 1, 'metric' => 'nb_visits', 'report' => 'MultiSites.getAll'),
            array('idsite' => 1, 'metric' => 'nb_visits', 'report' => 'NotExistingModule.Action'),
            array('idsite' => 1, 'metric' => 'bounce_rate', 'report' => 'Actions.getPageUrls'),
            array('idsite' => 1, 'metric' => 'not_valid', 'report' => 'Actions.getPageUrls')
        );

        $enriched = $this->notifier->enrichTriggeredAlerts($alerts);

        $alerts[0]['reportName']   = 'All Websites dashboard';
        $alerts[0]['reportMetric'] = 'Visits';
        $alerts[1]['reportName']   = null;
        $alerts[1]['reportMetric'] = null;
        $alerts[2]['reportName']   = 'Page URLs';
        $alerts[2]['reportMetric'] = 'Bounce Rate';
        $alerts[3]['reportName']   = 'Page URLs';
        $alerts[3]['reportMetric'] = null;

        $this->assertEquals($alerts, $enriched);
    }

    private function buildAlert($id, $name, $period = 'week', $idSite = 1, $siteName = 'Piwik test', $login = 'superUserLogin', $metric = 'nb_visits', $metricCondition = 'less_than', $metricMatched = 5000, $report = 'MultiSites.getOne', $reportCondition = 'matches_exactly', $reportMatched = 'Piwik')
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
            'phone_numbers' => array('+1234567890', '232'),
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