<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 * @category Piwik
 * @package Piwik
 */

namespace Piwik\Plugins\CustomAlerts;

use Piwik\Common;
use Piwik\Site;
use Piwik\Updater;
use Piwik\Updates;

/**
 * @package Updates
 */
class Updates_0_0_8 extends Updates
{
    static function getSql($schema = 'Myisam')
    {
        return array(
            "RENAME TABLE `" . Common::prefixTable('alert_log') . "` TO `" . Common::prefixTable('alert_triggered') . "`" => 1060,
            "ALTER TABLE `" . Common::prefixTable('alert_triggered') . "` ADD `name` VARCHAR(100) NOT NULL AFTER `value_new` " => 1060,
            "ALTER TABLE `" . Common::prefixTable('alert_triggered') . "` ADD `login` VARCHAR(100) NOT NULL AFTER `name` " => 1060,
            "ALTER TABLE `" . Common::prefixTable('alert_triggered') . "` ADD `period` VARCHAR(5) NOT NULL AFTER `login` " => 1060,
            "ALTER TABLE `" . Common::prefixTable('alert_triggered') . "` ADD `report` VARCHAR(150) NOT NULL AFTER `period` " => 1060,
            "ALTER TABLE `" . Common::prefixTable('alert_triggered') . "` ADD `report_condition` VARCHAR(50) AFTER `report` " => 1060,
            "ALTER TABLE `" . Common::prefixTable('alert_triggered') . "` ADD `report_matched` VARCHAR(1000) AFTER `report_condition` " => 1060,
            "ALTER TABLE `" . Common::prefixTable('alert_triggered') . "` ADD `metric` VARCHAR(150) NOT NULL AFTER `report_matched` " => 1060,
            "ALTER TABLE `" . Common::prefixTable('alert_triggered') . "` ADD `metric_condition` VARCHAR(50) NOT NULL AFTER `metric` " => 1060,
            "ALTER TABLE `" . Common::prefixTable('alert_triggered') . "` ADD `metric_matched` FLOAT NOT NULL AFTER `metric_condition` " => 1060,
            "ALTER TABLE `" . Common::prefixTable('alert_triggered') . "` ADD `compared_to` TINYINT NOT NULL AFTER `metric_matched` " => 1060,
            "ALTER TABLE `" . Common::prefixTable('alert_triggered') . "` ADD `email_me` BOOLEAN NOT NULL AFTER `compared_to` " => 1060,
            "ALTER TABLE `" . Common::prefixTable('alert_triggered') . "` ADD `additional_emails` TEXT DEFAULT '' AFTER `email_me` " => 1060,
            "ALTER TABLE `" . Common::prefixTable('alert_triggered') . "` ADD `phone_numbers` TEXT DEFAULT '' AFTER `additional_emails` " => 1060,
            "DELETE FROM `" . Common::prefixTable('alert_triggered') . "`" => 1060,
            "ALTER TABLE `" . Common::prefixTable('alert_triggered') . "` DROP KEY `ts_triggered` " => 1060,
            "ALTER TABLE `" . Common::prefixTable('alert_triggered') . "` ADD `idtriggered` BIGINT unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT FIRST " => 1060
        );
    }

    static function update()
    {
        Updater::updateDatabase(__FILE__, self::getSql());
    }
}
