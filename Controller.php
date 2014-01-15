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
    private $cachedReports = array();

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

        $notifier = new Notifier();
        $view->alertsFormatted = $notifier->formatAlerts($alerts, 'html');

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

        $reportUniqueId = $alert['report'];

        if (!empty($this->cachedReports[$idSite][$reportUniqueId])) {
            return $this->cachedReports[$idSite][$reportUniqueId];
        }

        $processedReport = new ProcessedReport();
        $report = $processedReport->getReportMetadataByUniqueId($idSite, $reportUniqueId);

        if (!array_key_exists($idSite, $this->cachedReports)) {
            $this->cachedReports[$idSite] = array();
        }

        $this->cachedReports[$idSite][$reportUniqueId] = $report;

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
        $idSites = SitesManagerApi::getInstance()->getSitesIdWithAtLeastViewAccess();
        return $idSites;
    }
}