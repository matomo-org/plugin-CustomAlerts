<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

use Piwik\Piwik;
use Piwik\Plugins\CustomAlerts\Model;

/**
 * Tracks custom events
 */
class Test_Piwik_Fixture_CustomAlerts extends Test_Piwik_BaseFixture
{
    public $dateTime = '2010-01-03 11:22:33';
    public $idSite = 1;

    public function setUp()
    {
        $this->setUpWebsites();
        $this->addAlerts();
    }

    private function setUpWebsites()
    {
        // tests run in UTC, the Tracker in UTC
        if (!self::siteCreated($idSite = 1)) {
            self::createWebsite($this->dateTime);
        }
    }

    private function addAlerts()
    {
        $this->createAlert('Test Alert 1', 'day', array($this->idSite), 'nb_visits', 'VisitsSummary_get');
        $this->createAlert('Test Alert 2', 'week', array($this->idSite), 'nb_uniq_visitors', 'Referrers_getSearchEngines');
        $this->createAlert('Test Alert 3', 'month', array($this->idSite), 'nb_hits', 'Actions_getPageUrls');

        // should not show up as different login
        $this->createAlert('Test Alert 4', 'month', array($this->idSite), 'nb_hits', 'Actions_getPageUrls', 'loginnotmatching');
    }

    private function createAlert($name, $period = 'week', $idSites = array(1), $metric = 'nb_visits', $report = 'MultiSites_getOne', $login = false)
    {
        if (false === $login) {
            $login = Piwik::getCurrentUserLogin();
        }

        $emails = array('test1@example.com', 'test2@example.com');
        $phoneNumbers = array('0123456789');

        $model = new Model();
        $model->createAlert($name, $idSites, $login, $period, 0, $emails, $phoneNumbers, $metric, 'less_than', 5, $comparedTo = 1, $report, 'matches_exactly', 'Piwik');
    }

    public function tearDown()
    {
    }

}
