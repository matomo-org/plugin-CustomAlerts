<?php

/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id$
 *
 */

namespace Piwik\Plugins\CustomAlerts;

use Exception;
use Piwik\Common;
use Piwik\Date;
use Piwik\Db;
use Piwik\DbHelper;
use Piwik\Period;
use Piwik\Translate;

/**
 *
 */
class Model
{

    public static function install()
    {
        $tableAlert = "`idalert` INT NOT NULL PRIMARY KEY ,
                       `name` VARCHAR(100) NOT NULL ,
                       `login` VARCHAR(100) NOT NULL ,
                       `period` VARCHAR(5) NOT NULL ,
                       `report` VARCHAR(150) NOT NULL ,
                       `report_condition` VARCHAR(50) ,
                       `report_matched` VARCHAR(255) ,
                       `metric` VARCHAR(150) NOT NULL ,
                       `metric_condition` VARCHAR(50) NOT NULL ,
                       `metric_matched` FLOAT NOT NULL ,
                       `compared_to` SMALLINT (4) UNSIGNED NOT NULL DEFAULT 1 ,
                       `email_me` BOOLEAN NOT NULL ,
                       `additional_emails` TEXT ,
                       `phone_numbers` TEXT ";

        DbHelper::createTable('alert', $tableAlert);

        $tableAlertSite = "`idalert` INT( 11 ) NOT NULL ,
                           `idsite` INT( 11 ) NOT NULL ,
                           PRIMARY KEY ( idalert, idsite )";

        DbHelper::createTable('alert_site', $tableAlertSite);

        $tableAlertLog = "`idtriggered` BIGINT unsigned NOT NULL AUTO_INCREMENT,
			              `idalert` INT( 11 ) NOT NULL ,
			              `idsite` INT( 11 ) NOT NULL ,
			              `ts_triggered` timestamp NOT NULL default CURRENT_TIMESTAMP,
			              `ts_last_sent` timestamp NULL DEFAULT NULL,
			              `value_old` DECIMAL (20,3) DEFAULT NULL,
			              `value_new` DECIMAL (20,3) DEFAULT NULL,
                          `name` VARCHAR(100) NOT NULL ,
			              `login` VARCHAR(100) NOT NULL ,
			              `period` VARCHAR(5) NOT NULL ,
			              `report` VARCHAR(150) NOT NULL ,
			              `report_condition` VARCHAR(50) ,
			              `report_matched` VARCHAR(1000) ,
			              `metric` VARCHAR(150) NOT NULL ,
			              `metric_condition` VARCHAR(50) NOT NULL ,
			              `metric_matched` FLOAT NOT NULL ,
			              `compared_to` SMALLINT NOT NULL DEFAULT 1 ,
			              `email_me` BOOLEAN NOT NULL ,
			              `additional_emails` TEXT ,
			              `phone_numbers` TEXT ,
			              PRIMARY KEY (idtriggered)";

        DbHelper::createTable('alert_triggered', $tableAlertLog);
    }

    public static function uninstall()
    {
        Db::dropTables(array(
            Common::prefixTable('alert'),
            Common::prefixTable('alert_triggered'),
            Common::prefixTable('alert_site')
        ));
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
     * @param bool|string $login   false returns alerts for all users
     *
     * @return array
     */
	public function getAlerts($idSites, $login = false)
	{
        $sql    = ("SELECT * FROM " . Common::prefixTable('alert')
                 . " WHERE idalert IN (" . $this->getInnerSiteQuery($idSites) . ") ");
        $values = array();

        if ($login !== false) {
            $sql     .= " AND login = ?";
            $values[] = $login;
        }

        $alerts = Db::fetchAll($sql, $values);
        $alerts = $this->completeAlerts($alerts);

        return $alerts;
	}

	public function getTriggeredAlertsForPeriod($period, $date)
	{
		$piwikDate = Date::factory($date);
		$date      = Period\Factory::build($period, $piwikDate);

        $db  = Db::get();
		$sql = $this->getTriggeredAlertsSelectPart()
               . " WHERE  period = ? AND ts_triggered BETWEEN ? AND ?";

        $values = array(
            $period,
            $date->getDateStart()->getDateStartUTC(),
            $date->getDateEnd()->getDateEndUTC()
        );

		$alerts = $db->fetchAll($sql, $values);
        $alerts = $this->completeAlerts($alerts);

        return $alerts;
	}

	public function getTriggeredAlerts($idSites, $login)
	{
        $idSites = array_map('intval', $idSites);

        $db  = Db::get();
		$sql = $this->getTriggeredAlertsSelectPart()
             . " WHERE idsite IN (" . implode(',' , $idSites) . ")"
             . " AND login = ?";
        $values = array($login);

		$alerts = $db->fetchAll($sql, $values);
        $alerts = $this->completeAlerts($alerts);

        return $alerts;
	}

    private function getTriggeredAlertsSelectPart()
    {
        return "SELECT * FROM   ". Common::prefixTable('alert_triggered');
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
        $sql = "SELECT * FROM ". Common::prefixTable('alert') . " WHERE period = ?";

        $alerts = Db::fetchAll($sql, array($period));
        $alerts = $this->completeAlerts($alerts);

        return $alerts;
	}

    /**
     * Creates an Alert for given website(s).
     *
     * @param string $name
     * @param int[] $idSites
     * @param string $login
     * @param string $period
     * @param bool $emailMe
     * @param array $additionalEmails
     * @param array $phoneNumbers
     * @param string $metric (nb_uniq_visits, sum_visit_length, ..)
     * @param string $metricCondition
     * @param float $metricValue
     * @param int $comparedTo
     * @param string $reportUniqueId
     * @param string $reportCondition
     * @param string $reportValue
     *
     * @throws \Exception
     * @internal param bool $enableEmail
     * @return int ID of new Alert
     */
	public function createAlert($name, $idSites, $login, $period, $emailMe, $additionalEmails, $phoneNumbers, $metric, $metricCondition, $metricValue, $comparedTo, $reportUniqueId, $reportCondition, $reportValue)
	{
        $idAlert = $this->getNextAlertId();

		$newAlert = array(
			'idalert'          => $idAlert,
			'name'             => $name,
			'period'           => $period,
			'login'            => $login,
			'email_me'         => $emailMe ? 1 : 0,
			'additional_emails' => json_encode($additionalEmails),
			'phone_numbers'    => json_encode($phoneNumbers),
			'metric'           => $metric,
			'metric_condition' => $metricCondition,
			'metric_matched'   => (float) $metricValue,
			'report'           => $reportUniqueId,
            'compared_to'      => $comparedTo,
            'report_condition' => $reportCondition,
            'report_matched'   => $reportValue
		);

        $db = Db::get();
		$db->insert(Common::prefixTable('alert'), $newAlert);

        $this->setSiteIds($idAlert, $idSites);

        return $idAlert;
	}

    /**
     * Edits an Alert for given website(s).
     *
     * @param $idAlert
     * @param string $name Name of Alert
     * @param int[] $idSites array of ints of idSites.
     * @param string $period Period the alert is defined on.
     * @param bool $emailMe
     * @param array $additionalEmails
     * @param array $phoneNumbers
     * @param string $metric (nb_uniq_visits, sum_visit_length, ..)
     * @param string $metricCondition
     * @param float $metricValue
     * @param int $comparedTo
     * @param string $reportUniqueId
     * @param string $reportCondition
     * @param string $reportValue
     *
     * @throws \Exception
     * @internal param bool $enableEmail
     * @return boolean
     */
	public function updateAlert($idAlert, $name, $idSites, $period, $emailMe, $additionalEmails, $phoneNumbers, $metric, $metricCondition, $metricValue, $comparedTo, $reportUniqueId, $reportCondition, $reportValue)
	{
		$alert = array(
			'name'             => $name,
			'period'           => $period,
			'email_me'         => $emailMe ? 1 : 0,
            'additional_emails' => json_encode($additionalEmails),
            'phone_numbers'    => json_encode($phoneNumbers),
			'metric'           => $metric,
			'metric_condition' => $metricCondition,
			'metric_matched'   => (float) $metricValue,
			'report'           => $reportUniqueId,
            'compared_to'      => $comparedTo,
            'report_condition' => $reportCondition,
            'report_matched'   => $reportValue
		);

        $db = Db::get();
		$db->update(Common::prefixTable('alert'), $alert, "idalert = " . intval($idAlert));

        $this->setSiteIds($idAlert, $idSites);

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
        $db->query("DELETE FROM " . Common::prefixTable("alert_triggered") . " WHERE idalert = ?", array($idAlert));
        $this->removeAllSites($idAlert);
    }

    public function triggerAlert($idAlert, $idSite, $valueNew, $valueOld, $datetime)
    {
        $alert      = $this->getAlert($idAlert);

        $keysToKeep = array('idalert', 'name', 'login', 'period', 'metric', 'metric_condition', 'metric_matched', 'report', 'report_condition', 'report_matched', 'compared_to', 'email_me', 'additional_emails', 'phone_numbers');

        $triggeredAlert = array();
        foreach ($keysToKeep as $key) {
            $triggeredAlert[$key] = $alert[$key];
        }

        $triggeredAlert['ts_triggered'] = $datetime;
        $triggeredAlert['ts_last_sent'] = null;
        $triggeredAlert['value_new'] = $valueNew;
        $triggeredAlert['value_old'] = $valueOld;
        $triggeredAlert['idsite']    = $idSite;
        $triggeredAlert['additional_emails'] = json_encode($triggeredAlert['additional_emails']);
        $triggeredAlert['phone_numbers'] = json_encode($triggeredAlert['phone_numbers']);

        $db = Db::get();
        $db->insert(
            Common::prefixTable('alert_triggered'),
            $triggeredAlert
        );
    }

    public function markTriggeredAlertAsSent($idTriggered, $timestamp)
    {
        $log = array(
            'ts_last_sent' => Date::factory($timestamp)->getDatetime()
        );

        $where = sprintf("idtriggered = %d", $idTriggered);

        $db = Db::get();
        $db->update(Common::prefixTable('alert_triggered'), $log, $where);
    }

    public function setSiteIds($idAlert, $idSites)
    {
        $this->removeAllSites($idAlert);

        $db = Db::get();
        foreach ($idSites as $idSite) {
            $db->insert(Common::prefixTable('alert_site'), array(
                'idalert' => intval($idAlert),
                'idsite' => intval($idSite)
            ));
        }
    }

    public function deleteTriggeredAlertsForSite($idSite)
    {
        Db::get()->query("DELETE FROM " . Common::prefixTable("alert_triggered") . " WHERE idsite = ?", $idSite);
    }

    private function getDefinedSiteIds($idAlert)
    {
        $sql   = "SELECT idsite FROM " . Common::prefixTable('alert_site') . " WHERE idalert = ?";
        $sites = Db::fetchAll($sql, $idAlert, \PDO::FETCH_COLUMN);

        $idSites = array();
        foreach ($sites as $site) {
            $idSites[] = $site['idsite'];
        }

        return $idSites;
    }

    private function getNextAlertId()
    {
        $idAlert = Db::fetchOne("SELECT max(idalert) + 1 FROM " . Common::prefixTable('alert'));

        if (empty($idAlert)) {
            $idAlert = 1;
        }

        return $idAlert;
    }

    private function completeAlerts($alerts)
    {
        foreach ($alerts as &$alert) {
            $alert['additional_emails'] = json_decode($alert['additional_emails']);
            $alert['phone_numbers']     = json_decode($alert['phone_numbers']);
            $alert['email_me']          = (int) $alert['email_me'];
            $alert['compared_to']       = (int) $alert['compared_to'];
            $alert['id_sites']          = $this->getDefinedSiteIds($alert['idalert']);
        }

        return $alerts;
    }

    private function removeAllSites($idAlert)
    {
        Db::get()->query("DELETE FROM " . Common::prefixTable("alert_site") . " WHERE idalert = ?", $idAlert);
    }

    /**
     * @param $idSites
     * @return array
     */
    protected function getInnerSiteQuery($idSites)
    {
        $idSites = array_map('intval', $idSites);

        $innerSiteQuery = "SELECT idalert FROM " . Common::prefixTable('alert_site')
                        . " WHERE idsite IN (" . implode(",", $idSites) . ")";

        return $innerSiteQuery;
    }

}
