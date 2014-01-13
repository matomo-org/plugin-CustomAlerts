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
use Piwik\Plugins\API\API as MetadataApi;
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

        $siteIds = SitesManagerApi::getInstance()->getSitesIdWithAtLeastViewAccess();
        $alerts  = API::getInstance()->getAlerts($siteIds);

        foreach ($alerts as &$alert) {
            $alert['reportName'] = $this->findReportName($alert);
            $alert['siteName']   = $this->findSiteName($alert);
        }

        $view->alerts = $alerts;
        $view->requirementsAreMet = $this->areRequirementsMet();

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

        list($module, $action) = explode('.', $alert['report']);

        $processedReport = new ProcessedReport();
        $metadata        = $processedReport->getMetadata($idSite, $module, $action);

        if (!empty($metadata)) {
            return array_shift($metadata);
        }
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
}