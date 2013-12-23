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

    public function test_formatAlerts_asTsv()
    {
        $alerts = $this->getTriggeredAlerts();

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

    public function test_sendAlertsPerEmailToRecipient()
    {
        $alerts = $this->getTriggeredAlerts();
        $mail   = new Mail();
        Mail::setDefaultTransport(new \Zend_Mail_Transport_File());

        $this->notifier->sendAlertsPerEmailToRecipient($alerts, $mail, 'test@example.com');

        $expectedHtml = <<<HTML
CustomAlerts_MailGreeting,<br /><br />=0A=0ACustomAlerts_MailText<br /><=
br />=0A=0A<table>=0A            <thead>=0A        <tr bg=
color=3D&#039;#c0c0c0&#039;>=0A                        <td>idal=
ert</td>=0A                        <td>idsite</td>=0A=
                        <td>alert_name</td>=0A             =
           <td>period</td>=0A                        <td&=
gt;site_name</td>=0A                        <td>login</td=
>=0A                        <td>report</td>=0A          =
              <td>report_condition</td>=0A                 =
       <td>report_matched</td>=0A                        &lt=
;td>metric</td>=0A                        <td>metric_cond=
ition</td>=0A                        <td>metric_matched</=
td>=0A                    </tr>=0A        </thead>=0A   =
     <tbody>=0A        <tr>=0A                <td>1&lt=
;/td>=0A                <td>1</td>=0A                <=
td>MyName1</td>=0A                <td>week</td>=0A=
                <td>Piwik test</td>=0A                <td=
>superUserLogin</td>=0A                <td>MultiSites.get=
One</td>=0A                <td>matches_exactly</td>=0A=
                <td>Piwik</td>=0A                <td>n=
b_visits</td>=0A                <td>less_than</td>=0A=
                <td>5</td>=0A            </tr>=0A    =
        </tbody>=0A            <tr>=0A                <td=
>2</td>=0A                <td>1</td>=0A           =
     <td>MyName2</td>=0A                <td>week</t=
d>=0A                <td>Piwik test</td>=0A             =
   <td>superUserLogin</td>=0A                <td>Multi=
Sites.getOne</td>=0A                <td>matches_exactly</=
td>=0A                <td>Piwik</td>=0A                &l=
t;td>nb_visits</td>=0A                <td>less_than</t=
d>=0A                <td>5</td>=0A            </tr>=
=0A    </table>=0A<br />=0ACustomAlerts_MailEnd
HTML;

        $expectedText = 'CustomAlerts_MailGreeting,=0A=0ACustomAlerts_MailText=0A=0Aidalert=09ids=
ite=09alert_name=09period=09site_name=09login=09report=09report_conditio=
n=09report_matched=09metric=09metric_condition=09metric_matched=0A1=091=
=09MyName1=09week=09Piwik test=09superUserLogin=09MultiSites.getOne=09ma=
tches_exactly=09Piwik=09nb_visits=09less_than=095=0A2=091=09MyName2=09we=
ek=09Piwik test=09superUserLogin=09MultiSites.getOne=09matches_exactly=
=09Piwik=09nb_visits=09less_than=095=0A=0A=0ACustomAlerts_MailEnd';


        $this->assertEquals($expectedHtml, html_entity_decode($mail->getBodyHtml(true)));
        $this->assertEquals($expectedText, $mail->getBodyText(true));
        $this->assertEquals(array('test@example.com'), $mail->getRecipients());
    }

    public function test_sendNewAlerts()
    {
        $mock = $this->getMock('Piwik\Plugins\CustomAlerts\tests\CustomNotifier', array('sendAlertsPerEmailToRecipient'));
        $alerts = array(
            $this->buildAlert(1, 'Alert1', 'week', 4, 'Test', 'login1'),
            $this->buildAlert(2, 'Alert2', 'week', 4, 'Test', 'login2'),
            $this->buildAlert(3, 'Alert3', 'week', 4, 'Test', 'login1'),
            $this->buildAlert(4, 'Alert4', 'week', 4, 'Test', 'login3'),
        );

        $mock->setTriggeredAlerts($alerts);

        $mock->expects($this->at(0))
             ->method('sendAlertsPerEmailToRecipient')
             ->with($this->equalTo(array($alerts[0], $alerts[2])),
                    $this->isInstanceOf('\Piwik\Mail'),
                    $this->equalTo('test1@example.com'));

        $mock->expects($this->at(1))
             ->method('sendAlertsPerEmailToRecipient')
            ->with($this->equalTo(array($alerts[1])),
                   $this->isInstanceOf('\Piwik\Mail'),
                   $this->equalTo('test2@example.com'));

        $mock->expects($this->at(2))
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
            'metric_matched' => $metricMatched
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