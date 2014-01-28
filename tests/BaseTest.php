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
use Piwik\Plugins\CustomAlerts\Model;
use Piwik\Plugins\CustomAlerts\API;
use Piwik\Translate;

/**
 * @group CustomAlerts
 * @group Database
 */
class BaseTest extends \DatabaseTestCase
{
    /**
     * @var \Piwik\Plugins\CustomAlerts\API
     */
    protected $api;

    /**
     * @var \Piwik\Plugins\CustomAlerts\Model
     */
    protected $model;

    protected $idSite;
    protected $idSite2;

    public function setUp()
    {
        parent::setUp();

        Model::install();

        $this->api   = API::getInstance();
        $this->model = new Model();

        $this->setSuperUser();
        $this->idSite  = \Test_Piwik_BaseFixture::createWebsite('2012-08-09 11:22:33');
        $this->idSite2 = \Test_Piwik_BaseFixture::createWebsite('2012-08-10 11:22:33');
    }

    public function tearDown()
    {
        Model::uninstall();

        parent::tearDown();
    }

    protected function setSuperUser()
    {
        $pseudoMockAccess = new \FakeAccess();
        \FakeAccess::setIdSitesAdmin(array(1, 2));
        \FakeAccess::setSuperUserAccess(true);
        \FakeAccess::$identity = 'superUserLogin';
        Access::setSingletonInstance($pseudoMockAccess);
    }

    protected function setUser()
    {
        $pseudoMockAccess = new \FakeAccess;
        \FakeAccess::setSuperUserAccess(false);
        \FakeAccess::$idSitesView = array(99);
        \FakeAccess::$identity = 'aUser';
        Access::setSingletonInstance($pseudoMockAccess);
    }

}
