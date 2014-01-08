<?php

/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id$
 *
 * @category Piwik_Plugins
 * @package Piwik_Alerts
 */

namespace Piwik\Plugins\CustomAlerts;

use Piwik\Piwik;
use Piwik\Db;
use Piwik\Menu\MenuTop;
use Piwik\ScheduledTask;
use Piwik\ScheduledTime;

/**
 *
 * @package Piwik_Alerts
 */
class CustomAlerts extends \Piwik\Plugin
{

	public function getListHooksRegistered()
	{
		return array(
		    'Menu.Top.addItems' => 'addTopMenu',
		    'TaskScheduler.getScheduledTasks' => 'getScheduledTasks',
		    'MobileMessaging.deletePhoneNumber' => 'removePhoneNumberFromAllAlerts',
		    'AssetManager.getJavaScriptFiles' => 'getJavaScriptFiles',
		    'AssetManager.getStylesheetFiles' => 'getStylesheetFiles',
		);
	}

	public function getJavaScriptFiles(&$jsFiles)
	{
		$jsFiles[] = "plugins/CustomAlerts/javascripts/alerts.js";
	}

	public function getStylesheetFiles(&$cssFiles)
	{
		$cssFiles[] = "plugins/CustomAlerts/stylesheets/alerts.less";
	}

    public function removePhoneNumberFromAllAlerts($phoneNumber)
    {
        $model  = new Model();
        $alerts = $model->getAllAlerts();

        foreach ($alerts as $alert) {
            if (empty($alert['phone_numbers']) || !is_array($alert['phone_numbers'])) {
                continue;
            }

            $key = array_search($phoneNumber, $alert['phone_numbers'], true);

            if (false !== $key) {
                unset($alert['phone_numbers'][$key]);
                $model->updateAlert(
                    $alert['idalert'],
                    $alert['name'],
                    $alert['idSites'],
                    $alert['period'],
                    $alert['email_me'],
                    $alert['additional_emails'],
                    array_values($alert['phone_numbers']),
                    $alert['metric'],
                    $alert['metric_condition'],
                    $alert['metric_matched'],
                    $alert['compared_to'],
                    $alert['report'],
                    $alert['report_condition'],
                    $alert['report_matched']
                );
            }
        }
    }

	public function install()
	{
		Model::install();
	}

	public function uninstall()
	{
		Model::uninstall();
	}

	public function addTopMenu()
	{
        $title = Piwik::translate('CustomAlerts_Alerts');

        MenuTop::addEntry($title, array("module" => "CustomAlerts", "action" => "index"), true, 9);
	}

	public function getScheduledTasks(&$tasks)
	{
        $processor = new Processor();
        $notifier  = new Notifier();

		$tasks[] = new ScheduledTask(
            $processor,
		    'processAlerts',
            'day',
		    ScheduledTime::factory('daily', 1)
		);

        $tasks[] = new ScheduledTask(
            $notifier,
            'sendNewAlerts',
            'day',
            ScheduledTime::factory('daily')
        );

		$tasks[] = new ScheduledTask(
            $processor,
		    'processAlerts',
            'week',
            ScheduledTime::factory('weekly')
		);

        $tasks[] = new ScheduledTask(
            $notifier,
            'sendNewAlerts',
            'week',
            ScheduledTime::factory('weekly')
        );

		$tasks[] = new ScheduledTask(
            $processor,
		    'processAlerts',
            'month',
            ScheduledTime::factory('monthly')
		);

        $tasks[] = new ScheduledTask(
            $notifier,
            'sendNewAlerts',
            'month',
            ScheduledTime::factory('monthly')
        );
	}
}