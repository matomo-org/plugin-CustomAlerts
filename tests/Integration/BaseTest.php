<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\CustomAlerts\tests\Integration;

use Piwik\Plugins\CustomAlerts\API;
use Piwik\Plugins\CustomAlerts\Model;
use Piwik\Tests\Framework\Mock\FakeAccess;
use Piwik\Tests\Framework\Fixture;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * @group CustomAlerts
 * @group Plugins
 */
abstract class BaseTest extends IntegrationTestCase
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

    public function setUp(): void
    {
        parent::setUp();

        $this->api   = API::getInstance();
        $this->model = new Model();

        $this->setSuperUser();
        $this->idSite  = Fixture::createWebsite('2012-08-09 11:22:33');
        $this->idSite2 = Fixture::createWebsite('2012-08-10 11:22:33');
    }

    protected function setSuperUser()
    {
        FakeAccess::setIdSitesAdmin(array(1, 2));
        FakeAccess::$superUser = true;
        FakeAccess::$identity  = 'superUserLogin';
    }

    protected function setUser()
    {
        FakeAccess::$superUser    = false;
        FakeAccess::$idSitesAdmin = array();
        FakeAccess::$idSitesView  = array(99);
        FakeAccess::$identity     = 'aUser';
    }

    /**
     * Create a test case
     * (prevent issue: https://travis-ci.org/piwik/piwik/jobs/34659383 )
     */
    public function testTrue()
    {
        $this->assertTrue(true);
    }

    public function provideContainerConfig()
    {
        return array(
            'Piwik\Access' => new FakeAccess()
        );
    }
}