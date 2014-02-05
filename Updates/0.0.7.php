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
use Piwik\Site;
use Piwik\Updater;
use Piwik\Updates;

/**
 */
class Updates_0_0_7 extends Updates
{
    static function getSql()
    {
        return array(
            "ALTER TABLE `" . Common::prefixTable('alert_log') . "` CHANGE `value_old` `value_old` DECIMAL (20,3) DEFAULT NULL" => 1060,
            "ALTER TABLE `" . Common::prefixTable('alert_log') . "` CHANGE `value_new` `value_new` DECIMAL (20,3) DEFAULT NULL" => 1060
        );
    }

    static function update()
    {
        Updater::updateDatabase(__FILE__, self::getSql());
    }
}
