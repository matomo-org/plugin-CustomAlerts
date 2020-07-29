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
use Piwik\Updater\Migration\Db as DbMigration;
use Piwik\Updater\Migration\Factory as MigrationFactory;
use Piwik\Updates;

/**
 */
class Updates_0_0_8 extends Updates
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
        $triggeredTable = Common::prefixTable('alert_triggered');

        return array(
            $this->migration->db->sql("RENAME TABLE `" . Common::prefixTable('alert_log') . "` TO `" . Common::prefixTable('alert_triggered') . "`",
                array(DbMigration::ERROR_CODE_DUPLICATE_COLUMN, DbMigration::ERROR_CODE_TABLE_NOT_EXISTS, DbMigration::ERROR_CODE_TABLE_EXISTS)),
            $this->migration->db->addColumns('alert_triggered', array(
                'name'              => 'VARCHAR(100) NOT NULL',
                'login'             => 'VARCHAR(100) NOT NULL',
                'period'            => 'VARCHAR(5) NOT NULL',
                'report'            => 'VARCHAR(150) NOT NULL',
                'report_condition'  => 'VARCHAR(50)',
                'report_matched'    => 'VARCHAR(1000)',
                'metric'            => 'VARCHAR(150) NOT NULL',
                'metric_condition'  => 'VARCHAR(50) NOT NULL',
                'metric_matched'    => 'FLOAT NOT NULL',
                'compared_to'       => 'TINYINT NOT NULL',
                'email_me'          => 'BOOLEAN NOT NULL',
                'additional_emails' => 'TEXT',
                'phone_numbers'     => 'TEXT',
            ), 'value_new'),
            $this->migration->db->sql("DELETE FROM `$triggeredTable`", DbMigration::ERROR_CODE_DUPLICATE_COLUMN),
            $this->migration->db->sql("ALTER TABLE `$triggeredTable` DROP KEY `ts_triggered` ", array(DbMigration::ERROR_CODE_DUPLICATE_COLUMN, DbMigration::ERROR_CODE_COLUMN_NOT_EXISTS)),
            $this->migration->db->sql("ALTER TABLE `$triggeredTable` ADD `idtriggered` BIGINT unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT FIRST ", DbMigration::ERROR_CODE_DUPLICATE_COLUMN)
        );
    }
}
