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

		return $view->render();
	}

    private function findReportName($alert)
    {
        if (empty($alert['report']) || empty($alert['id_sites'])) {
            return;
        }

        list($idSite) = $alert['id_sites'];

        list($module, $action) = explode('.', $alert['report']);

        $processedReport = new ProcessedReport();
        $metadata        = $processedReport->getMetadata($idSite, $module, $action);

        if (!empty($metadata)) {
            $report = array_shift($metadata);
            return $report['name'];
        }
    }

    private function findSiteName($alert)
    {
        if (empty($alert['id_sites'])) {
            return '';
        }

        list($idSite) = $alert['id_sites'];

        return Site::getNameFor($idSite);
    }

	public function addNewAlert()
	{
        $view = new View('@CustomAlerts/addNewAlert');
		$this->setGeneralVariablesView($view);

		$view->sitesList = $this->getSitesWithAtLeastViewAccess();

        $view->alertGroups           = array();
		$view->alertGroupConditions  = Processor::getGroupConditions();
		$view->alertMetricConditions = Processor::getMetricConditions();
		$view->comparablesDates = Processor::getComparablesDates();

		return $view->render();
	}

    private function getSitesWithAtLeastViewAccess()
    {
        return SitesManagerApi::getInstance()->getSitesWithAtLeastViewAccess();
    }

	public function editAlert()
	{
		$idAlert = Common::getRequestVar('idAlert', null, 'int');

        $view = new View('@CustomAlerts/editAlert');
		$this->setGeneralVariablesView($view);

		$view->alert     = API::getInstance()->getAlert($idAlert);
		$view->sitesList = $this->getSitesWithAtLeastViewAccess();
		$view->reportMetadata        = MetadataApi::getInstance()->getReportMetadata();
		$view->alertGroupConditions  = Processor::getGroupConditions();
		$view->alertMetricConditions = Processor::getMetricConditions();
        $view->comparablesDates = Processor::getComparablesDates();

		return $view->render();
	}
}