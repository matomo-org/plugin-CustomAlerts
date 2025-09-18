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
class Updates_5_1_0 extends Updates
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
        $emailPhoneJson = json_encode(["email","mobile"]);
        $emailJson = json_encode(["email"]);
        $phoneJson = json_encode(["mobile"]);
        $emptyJson = json_encode([]);
        return array(
            $this->migration->db->addColumn('alert', 'report_mediums', 'TEXT NOT NULL', 'report_matched'),
            $this->migration->db->addColumn('alert_triggered', 'report_mediums', 'TEXT NOT NULL', 'report_matched'),
            $this->migration->db->addColumn('alert', 'slack_channel_id', 'TEXT NULL', 'phone_numbers'),
            $this->migration->db->addColumn('alert_triggered', 'slack_channel_id', 'TEXT NULL', 'phone_numbers'),
            $this->migration->db->sql("UPDATE `$alertTableName` set report_mediums=CASE WHEN (email_me=1 OR additional_emails!='[]') AND phone_numbers!='[]' THEN '$emailPhoneJson' WHEN (email_me=1 OR additional_emails!='[]') AND phone_numbers='[]' THEN '$emailJson' WHEN (email_me!=1 AND additional_emails='[]') AND phone_numbers!='[]' THEN '$phoneJson' ELSE '$emptyJson' END"),
            $this->migration->db->sql("UPDATE `$alertTriggeredTableName` set report_mediums=CASE WHEN (email_me=1 OR additional_emails!='[]') AND phone_numbers!='[]' THEN '$emailPhoneJson' WHEN (email_me=1 OR additional_emails!='[]') AND phone_numbers='[]' THEN '$emailJson' WHEN (email_me!=1 AND additional_emails='[]') AND phone_numbers!='[]' THEN '$phoneJson' ELSE '$emptyJson' END"),
        );
    }
}
