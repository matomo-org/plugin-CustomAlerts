<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\CustomAlerts\tests;

use Piwik\Common;
use Piwik\Date;
use Piwik\Mail;
use Piwik\Plugin;
use Piwik\Plugins\CustomAlerts\Notifier;
use Piwik\Translate;
use Piwik\Url;

class CustomNotifier extends Notifier
{
    private $alerts = array();

    protected function getToday()
    {
        return Date::factory('2010-01-01');
    }

    protected function getTriggeredAlerts($period, $idSite)
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

    public function sendAlertsPerEmailToRecipient($alerts, \Piwik\Mail $mail, $recipient, $period, $idSite)
    {
        parent::sendAlertsPerEmailToRecipient($alerts, $mail, $recipient, $period, $idSite);
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
class NotifierTest extends BaseTest
{
    /**
     * @var CustomNotifier
     */
    private $notifier;

    public function setUp()
    {
        parent::setUp();

        // make sure templates will be found
        Plugin\Manager::getInstance()->loadPlugin('CustomAlerts');
        Plugin\Manager::getInstance()->loadPlugin('Zeitgeist');

        Translate::reloadLanguage('en');

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
MyName1 has been triggered as the metric Visits in report Single Website dashboard decreased more than 5000 from 228.128 to 4493.
>> Edit Alert ${host}index.php?module=CustomAlerts&action=editAlert&idAlert=1&idSite=1&period=week&date=yesterday&token_auth=

MyName2 has been triggered as the metric Visits in report Single Website dashboard decreased more than 5000 from 228.128 to 4493.
>> Edit Alert ${host}index.php?module=CustomAlerts&action=editAlert&idAlert=2&idSite=1&period=week&date=yesterday&token_auth=


FORMATTED;

        $rendered = $this->notifier->formatAlerts($alerts, 'text');

        $this->assertEquals($expected, $rendered);
    }

    public function test_formatAlerts_asSms()
    {
        $alerts = $this->getTriggeredAlerts();

        $expected = <<<FORMATTED
MyName1 has been triggered for website Piwik test as the metric Visits in report Single Website dashboard decreased more than 5000 from 228.128 to 4493. MyName2 has been triggered for website Piwik test as the metric Visits in report Single Website dashboard decreased more than 5000 from 228.128 to 4493.
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
<table style="border-collapse: collapse;width:100%" class="tableForm dataTable entityTable">
    <thead style="background-color:rgb(228,226,215);color:rgb(37,87,146);">
    <tr>
        <th style="padding:6px 6px;text-align: left;">Alert Name</th>
        <th style="padding:6px 6px;text-align: left;">Report</th>
        <th style="padding:6px 6px;text-align: left;">Alert Condition</th>
        <th style="padding:6px 6px;text-align: left;">Alert</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style="max-width:300px;border-bottom:1px solid rgb(231,231,231);padding:5px 0 5px 6px;"><a href="${host}index.php?module=CustomAlerts&action=editAlert&idAlert=1&idSite=1&period=week&date=yesterday&token_auth=">MyName1</a></td>
        <td style="max-width:300px;border-bottom:1px solid rgb(231,231,231);padding:5px 0 5px 6px;">Single Website dashboard</td>
        <td style="max-width:300px;border-bottom:1px solid rgb(231,231,231);padding:5px 0 5px 6px;">Website is 'Piwik'</td>
        <td style="max-width:300px;border-bottom:1px solid rgb(231,231,231);padding:5px 0 5px 6px;">Visits decreased more than 5000 from 228.128 to 4493</td>
    </tr>

    <tr>
        <td style="max-width:300px;border-bottom:1px solid rgb(231,231,231);padding:5px 0 5px 6px;"><a href="${host}index.php?module=CustomAlerts&action=editAlert&idAlert=2&idSite=1&period=week&date=yesterday&token_auth=">MyName2</a></td>
        <td style="max-width:300px;border-bottom:1px solid rgb(231,231,231);padding:5px 0 5px 6px;">Single Website dashboard</td>
        <td style="max-width:300px;border-bottom:1px solid rgb(231,231,231);padding:5px 0 5px 6px;">Website is 'Piwik'</td>
        <td style="max-width:300px;border-bottom:1px solid rgb(231,231,231);padding:5px 0 5px 6px;">Visits decreased more than 5000 from 228.128 to 4493</td>
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

        $this->notifier->sendAlertsPerEmailToRecipient($alerts, $mail, 'test@example.com', 'day', 1);

        $expectedHtml = <<<HTML
Hello,<br /><br />=0A=0AThe triggered alerts are listed in the table bel=
ow. To adjust your custom alert settings, please sign in and access the=
 Alerts page.<br /><br />=0A=0A<table
HTML;

        $expectedText = 'Hello,=0A=0AThe triggered alerts are listed in the table below. To adjus=
t your custom alert settings, please sign in and access the Alerts page.=
=0A=0A';

        $this->assertStringStartsWith($expectedHtml, html_entity_decode($mail->getBodyHtml(true)));
        $this->assertStringStartsWith($expectedText, $mail->getBodyText(true));
        $this->assertEquals(array('test@example.com'), $mail->getRecipients());
    }

    public function test_sendAlertsPerEmailToRecipient_shouldUseDifferentSubjectDependingOnPeriod()
    {
        $this->assertDateInSubject('day', 'Thursday 31 December 2009');
        $this->assertDateInSubject('week', 'Week 21 December - 27 December 2009');
        $this->assertDateInSubject('month', '2009, December');
    }

    private function assertDateInSubject($period, $expectedDate)
    {
        $alerts = $this->getTriggeredAlerts();
        Mail::setDefaultTransport(new \Zend_Mail_Transport_File());

        $mail = new Mail();
        $this->notifier->sendAlertsPerEmailToRecipient($alerts, $mail, 'test@example.com', $period, 1);
        $this->assertEquals('New alert for website Piwik test [' . $expectedDate . ']', $mail->getSubject());
    }

    public function test_sendNewAlerts()
    {
        $methods = array('sendAlertsPerEmailToRecipient', 'sendAlertsPerSmsToRecipient', 'markAlertAsSent');
        $mock    = $this->getMock('Piwik\Plugins\CustomAlerts\tests\CustomNotifier', $methods);

        $alerts = array(
            $this->buildAlert(1, 'Alert1', 'week', 4, 'Test', 'login1'),
            $this->buildAlert(2, 'Alert2', 'week', 4, 'Test', 'login2'),
            $this->buildAlert(3, 'Alert3', 'week', 4, 'Test', 'login1'),
            $this->buildAlert(4, 'Alert4', 'week', 4, 'Test', 'login3'),
        );

        $alerts[2]['phone_numbers'] = array('232');

        $idSite = 1;
        $period = 'week';

        $mock->setTriggeredAlerts($alerts);

        foreach ($alerts as $index => $alert) {
            $mock->expects($this->at($index))->method('markAlertAsSent')->with($this->equalTo($alert));
        }

        $mock->expects($this->at(4))
             ->method('sendAlertsPerEmailToRecipient')
             ->with($this->equalTo($alerts),
                    $this->isInstanceOf('\Piwik\Mail'),
                    $this->equalTo('test5@example.com'),
                    $this->equalTo($period),
                    $this->equalTo($idSite));

        $mock->expects($this->at(5))
             ->method('sendAlertsPerEmailToRecipient')
             ->with($this->equalTo(array($alerts[0], $alerts[2])),
                    $this->isInstanceOf('\Piwik\Mail'),
                    $this->equalTo('test1@example.com'),
                    $this->equalTo($period),
                    $this->equalTo($idSite));

        $mock->expects($this->at(6))
             ->method('sendAlertsPerEmailToRecipient')
             ->with($this->equalTo(array($alerts[1])),
                    $this->isInstanceOf('\Piwik\Mail'),
                    $this->equalTo('test2@example.com'),
                    $this->equalTo($period),
                    $this->equalTo($idSite));

        $mock->expects($this->at(7))
             ->method('sendAlertsPerEmailToRecipient')
             ->with($this->equalTo(array($alerts[3])),
                    $this->isInstanceOf('\Piwik\Mail'),
                    $this->equalTo('test3@example.com'),
                    $this->equalTo($period),
                    $this->equalTo($idSite));

        $mock->expects($this->at(8))
             ->method('sendAlertsPerSmsToRecipient')
             ->with($this->equalTo(array($alerts[0], $alerts[1], $alerts[3])),
                    $this->isInstanceOf('\Piwik\Plugins\MobileMessaging\API'),
                    $this->equalTo('+1234567890'));

        $mock->expects($this->at(9))
             ->method('sendAlertsPerSmsToRecipient')
             ->with($this->equalTo($alerts),
                    $this->isInstanceOf('\Piwik\Plugins\MobileMessaging\API'),
                    $this->equalTo('232'));

        $mock->sendNewAlerts($period, $idSite);
    }

    public function test_enrichTriggeredAlerts_shouldEnrichAlerts_IfReportExistsAndMetricIsValid()
    {
        $alerts = array(
            array('idsite' => 1, 'metric' => 'nb_visits', 'report' => 'MultiSites_getAll', 'report_condition' => 'matches_any', 'value_old' => '228.001', 'value_new' => '1.0'),
            array('idsite' => 1, 'metric' => 'nb_visits', 'report' => 'NotExistingModule_Action', 'report_condition' => 'matches_exactly', 'value_old' => '228.000', 'value_new' => '1.0'),
            array('idsite' => 1, 'metric' => 'bounce_rate', 'report' => 'Actions_getPageUrls', 'report_condition' => 'matches_exactly', 'value_old' => '228.999', 'value_new' => '1.0'),
            array('idsite' => 1, 'metric' => 'not_valid', 'report' => 'Actions_getPageUrls', 'report_condition' => 'contains', 'value_old' => '228.001', 'value_new' => '1.01'),
            // no dimension
            array('idsite' => 1, 'metric' => 'nb_visits', 'report' => 'VisitsSummary_get', 'report_condition' => 'matches_any', 'value_old' => '228.001', 'value_new' => '10')
        );

        $enriched = $this->notifier->enrichTriggeredAlerts($alerts);

        $alerts[0]['reportName']   = 'All Websites dashboard';
        $alerts[0]['reportMetric'] = 'Visits';
        $alerts[0]['dimension']    = 'Website';
        $alerts[0]['reportConditionName'] = 'matches any';
        $alerts[0]['value_old']    = '228.001';
        $alerts[0]['value_new']    = 1;
        $this->assertInternalType('int', $alerts[0]['value_new']);
        $alerts[1]['reportName']   = null;
        $alerts[1]['reportMetric'] = null;
        $alerts[1]['dimension']    = null;
        $alerts[1]['reportConditionName'] = null;
        $alerts[1]['value_old']    = 228;
        $alerts[1]['value_new']    = 1;
        $this->assertInternalType('int', $alerts[1]['value_old']);
        $this->assertInternalType('int', $alerts[1]['value_new']);
        $alerts[2]['reportName']   = 'Page URLs';
        $alerts[2]['reportMetric'] = 'Bounce Rate';
        $alerts[2]['dimension']    = 'Page URL';
        $alerts[2]['reportConditionName'] = 'is';
        $alerts[2]['value_old']    = '228.999';
        $alerts[2]['value_new']    = '1';
        $alerts[3]['reportName']   = 'Page URLs';
        $alerts[3]['reportMetric'] = null;
        $alerts[3]['dimension']    = 'Page URL';
        $alerts[3]['reportConditionName'] = 'contains';
        $alerts[3]['value_old']    = '228.001';
        $alerts[3]['value_new']    = '1.01';
        $alerts[4]['reportName']   = 'Visits Summary';
        $alerts[4]['reportMetric'] = 'Visits';
        $alerts[4]['dimension']    = null;
        $alerts[4]['reportConditionName'] = 'matches any';
        $alerts[4]['value_old']    = '228.001';
        $alerts[4]['value_new']    = 10;
        $this->assertInternalType('int', $alerts[4]['value_new']);

        $this->assertEquals($alerts, $enriched);
    }

    private function buildAlert($id, $name, $period = 'week', $idSite = 1, $siteName = 'Piwik test', $login = 'superUserLogin', $metric = 'nb_visits', $metricCondition = 'decrease_more_than', $metricMatched = 5000, $report = 'MultiSites_getOne', $reportCondition = 'matches_exactly', $reportMatched = 'Piwik')
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
            'value_new' => '4493.000',
            'value_old' => '228.128'
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

}