<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\CustomAlerts;

use Piwik\Updater;
use Piwik\Updater\Migration\Factory as MigrationFactory;
use Piwik\Updates;

/**
 */
class Updates_0_0_3 extends Updates
{
    /**
     * @var MigrationFactory
     */
    private $migration;

    public function __construct(MigrationFactory $factory)
    {
        $this->migration = $factory;
    }

    public function getMigrations(Updater $updater)
    {
        return array(
            $this->migration->db->addColumns('alert_log', array(
                'value_old' => 'BIGINT unsigned DEFAULT NULL',
                'value_new' => 'BIGINT unsigned DEFAULT NULL'
            ), 'ts_triggered')->addErrorCodeToIgnore(Updater\Migration\Db::ERROR_CODE_TABLE_NOT_EXISTS),
        );
    }

    public function doUpdate(Updater $updater)
    {
        $updater->executeMigrations(__FILE__, $this->getMigrations($updater));
    }
}
