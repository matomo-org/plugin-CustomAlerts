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
class Updates_0_0_2 extends Updates
{
    static function getSql()
    {
        return array(
            "ALTER TABLE `" . Common::prefixTable('alert') . "` CHANGE `enable_mail` `email_me` BOOLEAN NOT NULL" => array(1060, 1054),
            "ALTER TABLE `" . Common::prefixTable('alert') . "` ADD `additional_emails` TEXT AFTER `email_me` " => 1060,
            "ALTER TABLE `" . Common::prefixTable('alert') . "` ADD `phone_numbers` TEXT AFTER `additional_emails` " => 1060,
        );
    }

    static function update()
    {
        Updater::updateDatabase(__FILE__, self::getSql());
    }
}
