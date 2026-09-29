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
use Piwik\Db;
use Piwik\Updater;
use Piwik\Updater\Migration\Factory as MigrationFactory;
use Piwik\Updates;

/**
 * Moves alerts off the goal overview reports carrying `idGoal=0`. The ecommerce order report is chosen
 * where every site of the alert has ecommerce enabled. The Ecommerce plugin being inactive during the
 * update does not change the move, so the alert is evaluated again once the plugin is activated again.
 * Triggered alerts keep the report they were triggered for.
 */
class Updates_6_0_1 extends Updates
{
    /**
     * @var MigrationFactory
     */
    private $migration;

    public function __construct(MigrationFactory $factory)
    {
        $this->migration = $factory;
    }

    public function doUpdate(Updater $updater): void
    {
        $updater->executeMigrations(__FILE__, $this->getMigrations($updater));
    }

    public function getMigrations(Updater $updater): array
    {
        $alertTable = Common::prefixTable('alert');
        $siteTable  = Common::prefixTable('site');

        $hasRetiredReports = Db::fetchOne(
            "SELECT 1 FROM `$alertTable` WHERE report IN (?, ?, ?) LIMIT 1",
            ['Goals_get_idGoal--0', 'Goals_getVisitsUntilConversion_idGoal--0', 'Goals_getDaysToConversion_idGoal--0']
        );

        if (!$hasRetiredReports) {
            return [];
        }

        $alertSiteTable = Common::prefixTable('alert_site');

        $migrations = [
            $this->migration->db->boundSql(
                "UPDATE `$alertTable` a SET a.report = ?
                  WHERE a.report = ?
                    AND EXISTS (SELECT 1 FROM `$alertSiteTable` s WHERE s.idalert = a.idalert)
                    AND NOT EXISTS (
                        SELECT 1 FROM `$alertSiteTable` s
                        LEFT JOIN `$siteTable` site ON site.idsite = s.idsite
                        WHERE s.idalert = a.idalert AND (site.ecommerce IS NULL OR site.ecommerce <> 1)
                    )",
                ['Goals_get_idGoal--ecommerceOrder', 'Goals_get_idGoal--0']
            ),
        ];

        $replacements = [
            'Goals_get_idGoal--0'                      => 'Goals_get',
            'Goals_getVisitsUntilConversion_idGoal--0' => 'Goals_getVisitsUntilConversion',
            'Goals_getDaysToConversion_idGoal--0'      => 'Goals_getDaysToConversion',
        ];

        foreach ($replacements as $retired => $replacement) {
            $migrations[] = $this->migration->db->boundSql(
                "UPDATE `$alertTable` SET report = ? WHERE report = ?",
                [$replacement, $retired]
            );
        }

        return $migrations;
    }
}
