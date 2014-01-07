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

use Exception;
use Piwik\Piwik;
use Piwik\Common;
use Piwik\Date;
use Piwik\Period;
use Piwik\Db;
use Piwik\Translate;

/**
 *
 * @package Piwik_Alerts
 */
class Model
{

    public static function install()
    {
        $tableAlert = "CREATE TABLE " . Common::prefixTable('alert') . " (
			`idalert` INT NOT NULL PRIMARY KEY ,
			`name` VARCHAR(100) NOT NULL ,
			`login` VARCHAR(100) NOT NULL ,
			`period` VARCHAR(5) NOT NULL ,
			`report` VARCHAR(150) NOT NULL ,
			`report_condition` VARCHAR(50) ,
			`report_matched` VARCHAR(255) ,
			`metric` VARCHAR(150) NOT NULL ,
			`metric_condition` VARCHAR(50) NOT NULL ,
			`metric_matched` FLOAT NOT NULL ,
			`email_me` BOOLEAN NOT NULL ,
			`additional_emails` TEXT DEFAULT '' ,
			`phone_numbers` TEXT DEFAULT ''
		) DEFAULT CHARSET=utf8 ;";

        $tableAlertSite = "CREATE TABLE " . Common::prefixTable('alert_site') . "(
			`idalert` INT( 11 ) NOT NULL ,
			`idsite` INT( 11 ) NOT NULL ,
			PRIMARY KEY ( idalert, idsite )
		) DEFAULT CHARSET=utf8 ;";

        $tableAlertLog = "CREATE TABLE " . Common::prefixTable('alert_log') . " (
			`idalert` INT( 11 ) NOT NULL ,
			`idsite` INT( 11 ) NOT NULL ,
			`ts_triggered` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
			`value_old` BIGINT unsigned DEFAULT NULL,
			`value_new` BIGINT unsigned DEFAULT NULL,
			KEY `ts_triggered` (`ts_triggered`)
		)";

        try {
            Db::exec($tableAlert);
            Db::exec($tableAlertLog);
            Db::exec($tableAlertSite);
        } catch (Exception $e) {
            // mysql code error 1050:table already exists
            // see bug #153 http://dev.piwik.org/trac/ticket/153
            if (!Db::get()->isErrNo($e, '1050')) {
                throw $e;
            }
        }
    }

    public static function uninstall()
    {
        $tables = array('alert', 'alert_log', 'alert_site');
        foreach ($tables as $table) {
            $sql = "DROP TABLE IF EXISTS " . Common::prefixTable($table);
            Db::exec($sql);
        }
    }


    /**
     * Returns a single Alert
     *
     * @param int $idAlert
     *
     * @return array|null
     */
	public function getAlert($idAlert)
	{
        $query  = sprintf('SELECT * FROM %s WHERE idalert = ? LIMIT 1', Common::prefixTable('alert'));
		$alerts = Db::fetchAll($query, array(intval($idAlert)));

        if (empty($alerts)) {
            return;
        }

        $alerts = $this->completeAlerts($alerts);
		$alert  = array_shift($alerts);

		return $alert;
	}

    /**
     * Returns the Alerts that are defined on the idSites given.
     *
     * @param array $idSites
     * @return array
     */
	public function getAlerts($idSites)
	{
		$alerts = Db::fetchAll(("SELECT * FROM "
						. Common::prefixTable('alert')
						. " WHERE idalert IN (
					 SELECT pas.idalert FROM " . Common::prefixTable('alert_site')
						. "  pas WHERE idsite IN (" . implode(",", $idSites) . ")) "
		));

        $alerts = $this->completeAlerts($alerts);

        return $alerts;
	}

	public function getTriggeredAlerts($period, $date, $login)
	{
		$piwikDate = Date::factory($date);
		$date      = Period::factory($period, $piwikDate);

        $db = Db::get();

		$sql = "SELECT pa.idalert AS idalert,
				pal.idsite AS idsite,
				pal.ts_triggered AS ts_triggered,
				pa.name AS alert_name,
				pa.additional_emails AS additional_emails,
				pa.phone_numbers AS phone_numbers,
				pa.email_me AS email_me,
				ps.name AS site_name,
				login,
				period,
				report,
				report_condition,
				report_matched,
				metric,
				metric_condition,
				metric_matched,
				value_new,
				value_old
			FROM   ". Common::prefixTable('alert_log') ." pal
				JOIN ". Common::prefixTable('alert') ." pa
				ON pal.idalert = pa.idalert
				JOIN ". Common::prefixTable('site') ." ps
				ON pal.idsite = ps.idsite
			WHERE  period = ?
				AND ts_triggered BETWEEN ? AND ?";

        $values = array(
            $period,
            $date->getDateStart()->getDateStartUTC(),
            $date->getDateEnd()->getDateEndUTC()
        );

		if ($login !== false) {
			$sql     .= " AND login = ?";
            $values[] = $login;
		}

		$alerts = $db->fetchAll($sql, $values);
        $alerts = $this->completeAlerts($alerts);

        return $alerts;
	}

    public function getAllAlerts()
    {
        $sql = "SELECT * FROM ". Common::prefixTable('alert');

        $alerts = Db::fetchAll($sql);
        $alerts = $this->completeAlerts($alerts);

        return $alerts;
    }

	public function getAllAlertsForPeriod($period)
	{
		$sql = "SELECT * FROM "
				. Common::prefixTable('alert_site') . " alert, "
				. Common::prefixTable('alert') . " alert_site "
				. "WHERE alert.idalert = alert_site.idalert "
				. "AND period = ?";

		$alerts = Db::fetchAll($sql, array($period));
        $alerts = $this->completeAlerts($alerts);

        return $alerts;
	}

    /**
     * Creates an Alert for given website(s).
     *
     * @param string $name
     * @param mixed $idSites
     * @param string $period
     * @param bool $emailMe
     * @param array $additionalEmails
     * @param array $phoneNumbers
     * @param string $metric (nb_uniq_visits, sum_visit_length, ..)
     * @param string $metricCondition
     * @param float $metricValue
     * @param string $report
     * @param string $reportCondition
     * @param string $reportValue
     *
     * @throws \Exception
     * @internal param bool $enableEmail
     * @return int ID of new Alert
     */
	public function createAlert($name, $idSites, $period, $emailMe, $additionalEmails, $phoneNumbers, $metric, $metricCondition, $metricValue, $report, $reportCondition, $reportValue)
	{
        $idAlert = $this->getNextAlertId();
        if (empty($idAlert)) {
			$idAlert = 1;
		}

		$newAlert = array(
			'idalert'          => $idAlert,
			'name'             => $name,
			'period'           => $period,
			'login'            => Piwik::getCurrentUserLogin(),
			'email_me'         => (int) $emailMe,
			'additional_emails' => json_encode($additionalEmails),
			'phone_numbers'    => json_encode($phoneNumbers),
			'metric'           => $metric,
			'metric_condition' => $metricCondition,
			'metric_matched'   => (float) $metricValue,
			'report'           => $report
		);

		if (!empty($reportCondition) && !empty($reportCondition)) {
			$newAlert['report_condition'] = $reportCondition;
			$newAlert['report_matched']   = $reportValue;
		} else {
            $alert['report_condition'] = null;
            $alert['report_matched']   = null;
        }

        // save in db
        $db = Db::get();
		$db->insert(Common::prefixTable('alert'), $newAlert);
		foreach ($idSites as $idSite) {
			$db->insert(Common::prefixTable('alert_site'), array(
				'idalert' => intval($idAlert),
				'idsite'  => intval($idSite)
			));
		}

		return $idAlert;
	}

    /**
     * Edits an Alert for given website(s).
     *
     * @param $idAlert
     * @param string $name Name of Alert
     * @param mixed $idSites Single int or array of ints of idSites.
     * @param string $period Period the alert is defined on.
     * @param bool $emailMe
     * @param array $additionalEmails
     * @param array $phoneNumbers
     * @param string $metric (nb_uniq_visits, sum_visit_length, ..)
     * @param string $metricCondition
     * @param float $metricValue
     * @param string $report
     * @param string $reportCondition
     * @param string $reportValue
     *
     * @throws \Exception
     * @internal param bool $enableEmail
     * @return boolean
     */
	public function updateAlert($idAlert, $name, $idSites, $period, $emailMe, $additionalEmails, $phoneNumbers, $metric, $metricCondition, $metricValue, $report, $reportCondition, $reportValue)
	{
		$alert = array(
			'name'             => $name,
			'period'           => $period,
			'email_me'         => (int) $emailMe,
            'additional_emails' => json_encode($additionalEmails),
            'phone_numbers'    => json_encode($phoneNumbers),
			'metric'           => $metric,
			'metric_condition' => $metricCondition,
			'metric_matched'   => (float) $metricValue,
			'report'           => $report
		);

		if (!empty($reportCondition) && !empty($reportCondition)) {
			$alert['report_condition'] = $reportCondition;
			$alert['report_matched']   = $reportValue;
		} else {
			$alert['report_condition'] = null;
			$alert['report_matched']   = null;
		}

        // Save in DB
        $db = Db::get();
		$db->update(Common::prefixTable('alert'), $alert, "idalert = " . intval($idAlert));

		$db->query("DELETE FROM " . Common::prefixTable("alert_site") . "
					WHERE idalert = ?", $idAlert);

		foreach ($idSites as $idSite) {
			$db->insert(Common::prefixTable('alert_site'), array(
				'idalert' => intval($idAlert),
				'idsite'  => intval($idSite)
			));
		}

		return $idAlert;
	}

    /**
     * Delete alert by id.
     *
     * @param int $idAlert
     *
     * @throws \Exception In case alert does not exist or not enough permission
     */
	public function deleteAlert($idAlert)
	{
        $db = Db::get();
        $db->query("DELETE FROM " . Common::prefixTable("alert") . " WHERE idalert = ?", array($idAlert));

        $db = Db::get();
        $db->query("DELETE FROM " . Common::prefixTable("alert_site") . " WHERE idalert = ?", array($idAlert));

        $db = Db::get();
        $db->query("DELETE FROM " . Common::prefixTable("alert_log") . " WHERE idalert = ?", array($idAlert));
	}

    public function triggerAlert($idAlert, $idSite, $valueNew, $valueOld)
    {
        $db = Db::get();
        $db->insert(
            Common::prefixTable('alert_log'),
            array(
                'idalert' => intval($idAlert),
                'idsite'  => intval($idSite),
                'ts_triggered' => Date::now()->getDatetime(),
                'value_new' => $valueNew,
                'value_old' => $valueOld,
            )
        );
    }

    private function fetchSiteIdsTheAlertWasDefinedOn($idAlert)
    {
        $sql   = "SELECT idsite FROM ".Common::prefixTable('alert_site')." WHERE idalert = ?";
        $sites = Db::fetchAll($sql, $idAlert, \PDO::FETCH_COLUMN);

        $idSites = array();
        foreach ($sites as $site) {
            $idSites[] = $site['idsite'];
        }

        return $idSites;
    }

    private function getNextAlertId()
    {
        return Db::fetchOne("SELECT max(idalert) + 1 FROM " . Common::prefixTable('alert'));
    }

    private function completeAlerts($alerts)
    {
        if (empty($alerts)) {
            return $alerts;
        }

        foreach ($alerts as &$alert) {
            $alert['additional_emails'] = json_decode($alert['additional_emails']);
            $alert['phone_numbers']     = json_decode($alert['phone_numbers']);
            $alert['email_me']          = (bool) $alert['email_me'];
            $alert['idSites']           = $this->fetchSiteIdsTheAlertWasDefinedOn($alert['idalert']);
        }

        return $alerts;
    }

}