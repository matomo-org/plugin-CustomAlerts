<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\CustomAlerts;

use Piwik\Updater;
use Piwik\Updater\Migration\Factory as MigrationFactory;
use Piwik\Updates;

/**
 */
class Updates_5_2_6 extends Updates
{
    /**
     * @var MigrationFactory
     */
    private $migration;

    public function __construct(MigrationFactory $factory)
    {
        $this->migration = $factory;
    }

    public function doUpdate(Updater $updater)
    {
        $updater->executeMigrations(__FILE__, $this->getMigrations($updater));
    }

    public function getMigrations(Updater $updater): array
    {
        return array(
            $this->migration->db->addColumn('alert', 'description', "VARCHAR(255) NOT NULL DEFAULT ''", 'name'),
            $this->migration->db->addColumn('alert_triggered', 'description', "VARCHAR(255) NOT NULL DEFAULT ''", 'name'),
        );
    }
}
