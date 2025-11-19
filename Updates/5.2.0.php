<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\CustomAlerts;

use Piwik\Common;
use Piwik\Updater;
use Piwik\Updater\Migration\Factory as MigrationFactory;
use Piwik\Updates;

/**
 */
class Updates_5_2_0 extends Updates
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

    public function getMigrations(Updater $updater)
    {
        $alertTableName = Common::prefixTable('alert');
        $alertTriggeredTableName = Common::prefixTable('alert_triggered');

        return array(
            $this->migration->db->addColumn('alert', 'ms_teams_webhook_url', 'VARCHAR(500) NULL', 'slack_channel_id'),
            $this->migration->db->addColumn('alert_triggered', 'ms_teams_webhook_url', 'VARCHAR(500) NULL', 'slack_channel_id'),
        );
    }
}
