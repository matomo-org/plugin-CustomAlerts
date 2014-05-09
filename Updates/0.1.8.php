<?php
/**
 * Piwik - Open source web analytics
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
class Updates_0_1_8 extends Updates
{
    static function getSql()
    {
        return array(
            "ALTER TABLE `" . Common::prefixTable('alert_triggered') . "` CHANGE `compared_to` `compared_to` SMALLINT( 4 ) UNSIGNED NOT NULL DEFAULT 1" => 1060,
            "ALTER TABLE `" . Common::prefixTable('alert') . "` CHANGE `compared_to` `compared_to` SMALLINT( 4 ) UNSIGNED NOT NULL DEFAULT 1" => 1060,
        );
    }

    static function update()
    {
        Updater::updateDatabase(__FILE__, self::getSql());
    }
}
