<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\CustomAlerts\tests;

use Piwik\Access;
use Piwik\Db;
use Piwik\Piwik;
use Piwik\Plugins\CustomAlerts\CustomAlerts;
use Piwik\Plugins\CustomAlerts\Model;
use Piwik\Translate;

/**
 * @group CustomAlerts
 * @group CustomAlertsTest
 * @group Database
 */
class CustomAlertsTest extends BaseTest
{
    /**
     * @var \Piwik\Plugins\CustomAlerts\CustomAlerts
     */
    private $plugin = null;

    public function setUp()
    {
        parent::setUp();

        $this->plugin = new CustomAlerts();
    }

    public function test_getSiteIdsHavingAlerts()
    {
        $siteIds = $this->plugin->getSiteIdsHavingAlerts();
        $this->assertEquals(array(), $siteIds);


        $this->createAlert('Initial1', array(), array(1));
        $siteIds = $this->plugin->getSiteIdsHavingAlerts();
        $this->assertEquals(array(1), $siteIds);


        $this->createAlert('Initial2', array(), array(1, 3));
        $siteIds = $this->plugin->getSiteIdsHavingAlerts();
        $this->assertEquals(array(1, 3), $siteIds);


        $this->createAlert('Initial3', array(), array(2));
        $siteIds = $this->plugin->getSiteIdsHavingAlerts();
        $this->assertEquals(array(1, 3, 2), $siteIds);
    }

    public function test_removePhoneNumberFromAllAlerts()
    {
        $alert1 = $this->createAlert('Initial1', array());
        $alert2 = $this->createAlert('Initial2', null);
        $alert3 = $this->createAlert('Initial3', array('+123445679'));
        $alert4 = $this->createAlert('Initial4', array('123445679'));
        $alert5 = $this->createAlert('Initial5', array('123445679', '2384'));
        $alert6 = $this->createAlert('Initial6', array('+123445679', '123445679'));

        $this->plugin->removePhoneNumberFromAllAlerts('+123445679');

        $this->assertOnlyPhoneNumberChanged(1, $alert1, array());
        $this->assertOnlyPhoneNumberChanged(2, $alert2, null);
        $this->assertOnlyPhoneNumberChanged(3, $alert3, array());
        $this->assertOnlyPhoneNumberChanged(4, $alert4, array('123445679'));
        $this->assertOnlyPhoneNumberChanged(5, $alert5, array('123445679', '2384'));
        $this->assertOnlyPhoneNumberChanged(6, $alert6, array('123445679'));
    }

    private function createAlert($name, $phoneNumbers, $idSites = array(1))
    {
        $report  = 'MultiSites_getOne';
        $emails  = array('test1@example.com', 'test2@example.com');
        $login   = Piwik::getCurrentUserLogin();

        $id = $this->model->createAlert($name, $idSites, $login, 'week', 0, $emails, $phoneNumbers, 'nb_visits', 'less_than', 5, $comparedTo = 7, $report, 'matches_exactly', 'Piwik');

        return $this->model->getAlert($id);
    }

    private function assertOnlyPhoneNumberChanged($id, $alertBefore, $phoneNumbers)
    {
        $alert = $this->model->getAlert($id);

        $this->assertSame($phoneNumbers, $alert['phone_numbers']);

        $alertBefore['phone_numbers'] = $phoneNumbers;

        $this->assertSame($alertBefore, $alert);
    }

}