<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\CustomAlerts;

use Piwik\Common;
use Piwik\Updater;
use Piwik\Updates;

/**
 */
class Updates_0_0_3 extends Updates
{
    static function getSql()
    {
        return array(
            "ALTER TABLE `" . Common::prefixTable('alert_log') . "` ADD `value_old` BIGINT unsigned DEFAULT NULL AFTER `ts_triggered` " => array(1060, 1146),
            "ALTER TABLE `" . Common::prefixTable('alert_log') . "` ADD `value_new` BIGINT unsigned DEFAULT NULL AFTER `value_old` " => array(1060, 1146)
        );
    }

    static function update()
    {
        Updater::updateDatabase(__FILE__, self::getSql());
    }
}
