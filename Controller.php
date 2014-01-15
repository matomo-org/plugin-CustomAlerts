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

use Piwik\Date;
use Piwik\Piwik;
use Piwik\Plugins\API\ProcessedReport;
use Piwik\Site;
use Piwik\View;
use Piwik\Common;
use Piwik\Plugin\Manager as PluginManager;
use Piwik\Plugins\SitesManager\API as SitesManagerApi;
use Piwik\Period;
use Piwik\Db;

/**
  *
 * @package Piwik_Alerts
 */
class Controller extends \Piwik\Plugin\Controller
{
	/**
	 * Shows all Alerts of the current selected idSite.
	 */
	public function index()
	{
        $view = new View('@CustomAlerts/index');
        $this->setGeneralVariablesView($view);

        $idSites = $this->getSiteIdsHavingAccess();
        $alerts  = API::getInstance()->getAlerts($idSites);

        foreach ($alerts as &$alert) {
            $alert['reportName'] = $this->findReportName($alert);
            $alert['siteName']   = $this->findSiteName($alert);
        }

        $view->alerts = $alerts;
        $view->requirementsAreMet = $this->areRequirementsMet();

		return $view->render();
	}

	public function historyTriggeredAlerts()
	{
        $view = new View('@CustomAlerts/historyTriggeredAlerts');
        $this->setGeneralVariablesView($view);

        $idSites = $this->getSiteIdsHavingAccess();
        $alerts  = API::getInstance()->getTriggeredAlerts($idSites);
        array_slice($alerts, 0, 100);
        $alerts  = array_reverse($alerts);

        $view->alertsFormatted = $this->formatAlerts($alerts, 'html_extended');

		return $view->render();
	}

	public function addNewAlert()
	{
        $view = new View('@CustomAlerts/addNewAlert');
		$this->setGeneralVariablesView($view);
        $this->addBasicCreateAndEditVariables($view, null);

		return $view->render();
	}

	public function editAlert()
	{
		$idAlert = Common::getRequestVar('idAlert', null, 'int');

        $view = new View('@CustomAlerts/editAlert');
		$this->setGeneralVariablesView($view);

        $alert = API::getInstance()->getAlert($idAlert);
		$view->alertSiteName = $this->findSiteName($alert);
		$view->alertSiteId   = $this->findSiteId($alert);

        $this->addBasicCreateAndEditVariables($view, $alert);

		return $view->render();
	}

    private function addBasicCreateAndEditVariables($view, $alert)
    {
        $view->alert = $alert;
        $view->alertGroupConditions  = Processor::getGroupConditions();
        $view->alertMetricConditions = Processor::getMetricConditions();
        $view->comparablesDates   = Processor::getComparablesDates();
        $view->reportMetadata     = $this->findReportMetadata($alert);
        $view->requirementsAreMet = $this->areRequirementsMet();
        $view->supportsSMS        = $this->supportsSms();
    }

    private function getSitesWithAtLeastViewAccess()
    {
        return SitesManagerApi::getInstance()->getSitesWithAtLeastViewAccess();
    }

    private function areRequirementsMet()
    {
        return PluginManager::getInstance()->isPluginActivated('ScheduledReports');
    }

    private function supportsSms()
    {
        return PluginManager::getInstance()->isPluginActivated('MobileMessaging');
    }

    private function findReportMetadata($alert)
    {
        $idSite = $this->findSiteId($alert);

        if (empty($idSite)) {
            return;
        }

        $processedReport = new ProcessedReport();
        $report = $processedReport->getReportMetadataByUniqueId($idSite, $alert['report']);

        return $report;
    }

    private function findReportName($alert)
    {
        $report = $this->findReportMetadata($alert);

        if (!empty($report)) {
            return $report['name'];
        }
    }

    private function findSiteName($alert)
    {
        $idSite = $this->findSiteId($alert);

        if ($idSite) {

            return Site::getNameFor($idSite);
        }
    }

    private function findSiteId($alert)
    {
        if (empty($alert['id_sites'])) {
            return;
        }

        list($idSite) = $alert['id_sites'];

        return $idSite;
    }

    private function getSiteIdsHavingAccess()
    {
        return SitesManagerApi::getInstance()->getSitesIdWithAtLeastViewAccess();
    }

    /**
     * Returns the Alerts that were triggered in $format.
     *
     * @param array $triggeredAlerts
     * @param string $format Can be 'html' or 'tsv'
     * @throws \Exception
     * @return string
     */
    public function formatAlerts($triggeredAlerts, $format)
    {
        switch ($format) {
            case 'html_extended':
                $view = new View('@CustomAlerts/htmlTriggeredAlerts');
                $view->triggeredAlerts = $this->enrichTriggeredAlerts($triggeredAlerts);
                $view->extended        = true;

                return $view->render();

            case 'html':
                $view = new View('@CustomAlerts/htmlTriggeredAlerts');
                $view->triggeredAlerts = $this->enrichTriggeredAlerts($triggeredAlerts);
                $view->extended        = false;

                return $view->render();

            case 'sms':

                $view = new View('@CustomAlerts/smsTriggeredAlerts');
                $view->triggeredAlerts = $this->enrichTriggeredAlerts($triggeredAlerts);

                return $view->render();

            case 'text':

                $view = new View('@CustomAlerts/textTriggeredAlerts');
                $view->triggeredAlerts = $this->enrichTriggeredAlerts($triggeredAlerts);

                return $view->render();
        }

        throw new \Exception('Unsupported format');
    }

    protected function enrichTriggeredAlerts($triggeredAlerts)
    {
        $processedReport = new ProcessedReport();

        $cached = array();
        foreach ($triggeredAlerts as &$alert) {
            $idSite = $alert['idsite'];
            $metric = $alert['metric'];
            $report = $alert['report'];

            if (!array_key_exists($idSite, $cached)) {
                $cached[$idSite] = array('report' => array(), 'metric' => array(), 'siteName' => '');
            }

            if (empty($cached[$idSite]['siteName'])) {
                $cached[$idSite]['siteName'] = Site::getNameFor($idSite);
            }

            if (!array_key_exists($report, $cached[$idSite]['report'])) {
                $cached[$idSite]['report'][$report] = $processedReport->getReportMetadataByUniqueId($idSite, $alert['report']);
                $cached[$idSite]['metric'][$report] = array();
            }

            if (!array_key_exists($metric, $cached[$idSite]['metric'][$report])) {
                $cached[$idSite]['metric'][$report][$metric] = $processedReport->translateMetric($metric, $idSite, $alert['report']);
            }
        }

        foreach ($triggeredAlerts as &$alert) {
            $idSite = $alert['idsite'];
            $metric = $alert['metric'];
            $report = $alert['report'];

            $alert['value_old']    = (int) $alert['value_old'] == $alert['value_old'] ? (int) $alert['value_old'] : $alert['value_old'];
            $alert['value_new']    = (int) $alert['value_new'] == $alert['value_new'] ? (int) $alert['value_new'] : $alert['value_new'];
            $alert['reportName']   = null;
            $alert['dimension']    = null;
            $alert['reportMetric'] = !empty($cached[$idSite]['metric'][$report][$metric]) ? $cached[$idSite]['metric'][$report][$metric] : null;
            $alert['reportConditionName'] = null;
            $alert['siteName']     = $cached[$idSite]['siteName'];
            $alert['ts_triggered'] = Date::factory($alert['ts_triggered']);

            if (!empty($cached[$idSite]['report'][$report])) {
                $report = $cached[$idSite]['report'][$report];

                $alert['reportName'] = $report['name'];
                $alert['dimension']  = !empty($report['dimension']) ? $report['dimension'] : null;

                $conditionTranslation = array_search($alert['report_condition'], Processor::getGroupConditions(), true);
                $alert['reportConditionName'] = $conditionTranslation ? Piwik::translate($conditionTranslation) : null;
            }
        }

        return $triggeredAlerts;
    }

}