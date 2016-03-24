<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\CustomAlerts\tests\Integration;

use Piwik\Date;
use Piwik\Mail;
use Piwik\Piwik;
use Piwik\Plugin;
use Piwik\Plugins\CustomAlerts\Notifier;
use Piwik\Site;
use Piwik\Translate;
use Piwik\Period\Factory as PeriodFactory;

class CustomNotifier extends Notifier
{
    private $alerts = array();

    public function getToday()
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
 * @group Plugins
 */
class NotifierTest extends BaseTest
{
    /** @var CustomNotifier $notifier */
    private $notifier;

    public function setUp()
    {
        parent::setUp();

        // make sure templates will be found
        Plugin\Manager::getInstance()->loadPlugin('CustomAlerts');
        Plugin\Manager::getInstance()->loadPlugin('Morpheus');

        Translate::loadAllTranslations();

        \Piwik\Plugins\UsersManager\API::getInstance()->addUser('login1', 'p2kK2msAw1', 'test1@example.com');
        \Piwik\Plugins\UsersManager\API::getInstance()->addUser('login2', 'p2kK2msAw1', 'test2@example.com');
        \Piwik\Plugins\UsersManager\API::getInstance()->addUser('login3', 'p2kK2msAw1', 'test3@example.com');

        $this->notifier = new CustomNotifier();
    }

    public function tearDown()
    {
        parent::tearDown();

        Translate::reset();
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
        $this->assertDateInSubject('week', $this->getMockMail('week')->getSubject());
        $this->assertDateInSubject('day', $this->getMockMail('day')->getSubject());
        $this->assertDateInSubject('month', $this->getMockMail('month')->getSubject());
    }

    /**
     * @param $period
     * @return Mail
     */
    private function getMockMail($period) {
        $mail = new Mail();

        $websiteName = Site::getNameFor($this->idSite);
        $prettyDate  = $this->getPrettyDateForSite($period, $this->idSite);
        $mail->setSubject(Piwik::translate('CustomAlerts_MailAlertSubject', array($websiteName, $prettyDate)));

        return $mail;
    }

    private function getPrettyDateForSite($period, $idSite)
    {
        $timezone = Site::getTimezoneFor($idSite);

        $customToday = $this->notifier->getToday();
        $piwikDate = Date::factory($customToday->getDatetime(), $timezone);
        $dateWithSubPeriod = $piwikDate->subPeriod(1, $period);
        $period     = PeriodFactory::build($period, $dateWithSubPeriod);
        $prettyDate = $period->getLocalizedLongString();

        return $prettyDate;
    }

    private function assertDateInSubject($period, $expectedSubject)
    {
        $alerts = $this->getTriggeredAlerts();
        Mail::setDefaultTransport(new \Zend_Mail_Transport_File());

        $mail = new Mail();
        $this->notifier->sendAlertsPerEmailToRecipient($alerts, $mail, 'test@example.com', $period, $this->idSite);

        $expecteds = array(
            $expectedSubject,
            \Zend_Mime::encodeQuotedPrintableHeader($expectedSubject, 'utf-8')
        );

        $isExpected = in_array( $mail->getSubject(), $expecteds);
        $this->assertTrue($isExpected , $mail->getSubject() . " not found in " . var_export($expecteds, true));
    }

    public function test_sendNewAlerts()
    {
        $methods = array('sendAlertsPerEmailToRecipient', 'sendAlertsPerSmsToRecipient', 'markAlertAsSent');
        $mock    = $this->getMock('Piwik\Plugins\CustomAlerts\tests\Integration\CustomNotifier', $methods);

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
            'email_me' => 1,
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
