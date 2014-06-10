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
class Updates_0_1_17 extends Updates
{
    static function getSql()
    {
        return array(
            "ALTER TABLE `" . Common::prefixTable('alert') . "` CHANGE `email_me` `email_me` BOOLEAN NOT NULL DEFAULT '0'" => 1060,
            "ALTER TABLE `" . Common::prefixTable('alert_triggered') . "` CHANGE `email_me` `email_me` BOOLEAN NOT NULL DEFAULT '0'" => 1060,
        );
    }

    static function update()
    {
        Updater::updateDatabase(__FILE__, self::getSql());
    }
}
