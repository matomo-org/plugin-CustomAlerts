<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\CustomAlerts\tests;

use Piwik\Date;
use Piwik\Mail;
use Piwik\Plugin;
use Piwik\Plugins\CustomAlerts\Notifier;
use Piwik\Translate;

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

        $mock->expects($this->at(0))
             ->method('sendAlertsPerEmailToRecipient')
             ->with($this->equalTo($alerts),
                    $this->isInstanceOf('\Piwik\Mail'),
                    $this->equalTo('test5@example.com'),
                    $this->equalTo($period),
                    $this->equalTo($idSite));

        $mock->expects($this->at(1))
             ->method('sendAlertsPerEmailToRecipient')
             ->with($this->equalTo(array($alerts[0], $alerts[2])),
                    $this->isInstanceOf('\Piwik\Mail'),
                    $this->equalTo('test1@example.com'),
                    $this->equalTo($period),
                    $this->equalTo($idSite));

        $mock->expects($this->at(2))
             ->method('sendAlertsPerEmailToRecipient')
             ->with($this->equalTo(array($alerts[1])),
                    $this->isInstanceOf('\Piwik\Mail'),
                    $this->equalTo('test2@example.com'),
                    $this->equalTo($period),
                    $this->equalTo($idSite));

        $mock->expects($this->at(3))
             ->method('sendAlertsPerEmailToRecipient')
             ->with($this->equalTo(array($alerts[3])),
                    $this->isInstanceOf('\Piwik\Mail'),
                    $this->equalTo('test3@example.com'),
                    $this->equalTo($period),
                    $this->equalTo($idSite));

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

        foreach ($alerts as $index => $alert) {
            $mock->expects($this->at(6 + $index))->method('markAlertAsSent')->with($this->equalTo($alert));
        }

        $mock->sendNewAlerts($period, $idSite);
    }

    private function buildAlert($id, $name, $period = 'week', $idSite = 1, $siteName = 'Piwik test', $login = 'superUserLogin', $metric = 'nb_visits', $metricCondition = 'decrease_more_than', $metricMatched = 5000, $report = 'MultiSites_getOne', $reportCondition = 'matches_exactly', $reportMatched = 'Piwik')
    {
        return array(
            'idtriggered' => 1,
            'idalert' => $id,
            'idsite' => $idSite,
            'name' => $name,
            'period' => $period,
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
            'value_old' => '228.128',
            'ts_triggered' => time()
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
