<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

use Piwik\Piwik;
use Piwik\Plugins\CustomAlerts\Model;
use Piwik\Tests\Framework\Fixture;

/**
 * Tracks custom events
 */
class Test_Piwik_Fixture_CustomAlerts extends Fixture
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

        $this->triggerAlert(1, 10, 5, '2014-01-16 03:21:17');
        $this->triggerAlert(1, 2999, 10, '2014-01-15 03:21:17');
        $this->triggerAlert(2, 1004, 1, '2013-01-16 03:21:17');
        $this->triggerAlert(2, 10, 5, '2013-10-16 03:21:17');

        // should not show up as belongs to different login
        $this->triggerAlert(4, 10, 5, '2013-10-16 03:21:17');
    }

    private function triggerAlert($idAlert, $valueNew, $valueOld, $datetime)
    {
        $model = new Model();
        $model->triggerAlert($idAlert, $this->idSite, $valueNew, $valueOld, $datetime);
    }

    private function createAlert($name, $period, $idSites, $metric, $report, $login = false)
    {
        if (false === $login) {
            $login = Piwik::getCurrentUserLogin();
        }

        $emails = array('test1@example.com', 'test2@example.com');
        $phoneNumbers = array('0123456789');

        $reportMatched = '';
        if ('VisitsSummary_get' != $report) {
            $reportMatched = 'Piwik';
        }

        $model = new Model();
        $model->createAlert($name, $idSites, $login, $period, 0, $emails, $phoneNumbers, $metric, 'less_than', 5, $comparedTo = 1, $report, 'matches_exactly', $reportMatched);
    }

    public function tearDown()
    {
    }

}
