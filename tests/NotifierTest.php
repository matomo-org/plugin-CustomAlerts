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

        \Piwik\Plugins\UsersManager\API::getInstance()->addUser('login1', 'p2kK2msAw1', 'test1@example.com');
        \Piwik\Plugins\UsersManager\API::getInstance()->addUser('login2', 'p2kK2msAw1', 'test2@example.com');
        \Piwik\Plugins\UsersManager\API::getInstance()->addUser('login3', 'p2kK2msAw1', 'test3@example.com');

        $this->notifier = new CustomNotifier();
    }

    public function test_formatAlerts_asText()
    {
        $alerts = $this->getTriggeredAlerts();

        $expected = <<<FORMATTED
Name    Website    Period    Report    Condition
MyName1    Piwik test    week    MultiSites.getOne    nb_visits less_than 5
MyName2    Piwik test    week    MultiSites.getOne    nb_visits less_than 5

FORMATTED;

        $rendered = $this->notifier->formatAlerts($alerts, 'text');

        $this->assertEquals($expected, $rendered);
    }

    public function test_formatAlerts_asSms()
    {
        $alerts = $this->getTriggeredAlerts();

        $expected = <<<FORMATTED
The following alerts were triggered: MyName1 (website Piwik test), MyName2 (website Piwik test)
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
        <td>Name</td>
        <td>Website</td>
        <td>Period</td>
        <td>Report</td>
        <td>Condition</td>
    </tr>
    </thead>
    <tbody>

    <tr>
        <td>MyName1</td>
        <td>Piwik test</td>
        <td>week</td>
        <td>MultiSites.getOne</td>
        <td>nb_visits less_than 5</td>
    </tr>


    <tr>
        <td>MyName2</td>
        <td>Piwik test</td>
        <td>week</td>
        <td>MultiSites.getOne</td>
        <td>nb_visits less_than 5</td>
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
CustomAlerts_MailGreeting,<br /><br />=0A=0ACustomAlerts_MailText<br /><=
br />=0A=0A<table>=0A    <thead>=0A    <tr bgcolor=3D&#03=
9;#c0c0c0&#039;>=0A        <td>Name</td>=0A        <td=
>Website</td>=0A        <td>Period</td>=0A        &=
lt;td>Report</td>=0A        <td>Condition</td>=0A =
   </tr>=0A    </thead>=0A    <tbody>=0A=0A    <tr&=
gt;=0A        <td>MyName1</td>=0A        <td>Piwik tes=
t</td>=0A        <td>week</td>=0A        <td>Mul=
tiSites.getOne</td>=0A        <td>nb_visits less_than 5</=
td>=0A    </tr>=0A=0A=0A    <tr>=0A        <td>MyNa=
me2</td>=0A        <td>Piwik test</td>=0A        <t=
d>week</td>=0A        <td>MultiSites.getOne</td>=0A=
        <td>nb_visits less_than 5</td>=0A    </tr>=0A=
=0A    </tbody>=0A</table>=0A<br />=0ACustomAlerts_MailEnd
HTML;

        $expectedText = 'CustomAlerts_MailGreeting,=0A=0ACustomAlerts_MailText=0A=0AName    Websi=
te    Period    Report    Condition=0AMyName1    Piwik test    week    M=
ultiSites.getOne    nb_visits less_than 5=0AMyName2    Piwik test    wee=
k    MultiSites.getOne    nb_visits less_than 5=0A=0A=0ACustomAlerts_Mai=
lEnd';

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
            'email_me' => true
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