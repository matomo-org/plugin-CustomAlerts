<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 */

namespace Piwik\Plugins\CustomAlerts;

use Exception;
use OpenApi\Annotations as OA;
use Piwik\Common;
use Piwik\Piwik;
use Piwik\Site;

/**
 *
 * @method static \Piwik\Plugins\CustomAlerts\API getInstance()
 *
 * @OA\Schema(
 *     schema="alert",
 *     title="Alert",
 *     type="object",
 *     @OA\Property(
 *         property="idAlert",
 *         description="ID of the alert",
 *         type="integer",
 *     ),
 *     @OA\Property(
 *         property="name",
 *         description="Name of the alert",
 *         type="string",
 *     ),
 *     @OA\Property(
 *         property="login",
 *         description="Name of the login of the user which created the alert",
 *         type="string",
 *     ),
 *     @OA\Property(
 *         property="period",
 *         description="Name of the period such as day, week, or month",
 *         type="string",
 *     ),
 *     @OA\Property(
 *         property="report",
 *         description="Name of the report which the alert monitors",
 *         type="string",
 *     ),
 *     @OA\Property(
 *         property="report_condition",
 *         type="string",
 *     ),
 *     @OA\Property(
 *         property="report_matched",
 *         type="string",
 *     ),
 *     @OA\Property(
 *         property="metric",
 *         type="string",
 *     ),
 *     @OA\Property(
 *         property="metric_condition",
 *         type="string",
 *     ),
 *     @OA\Property(
 *         property="metric_matched",
 *         type="number",
 *     ),
 *     @OA\Property(
 *         property="compared_to",
 *         type="integer",
 *     ),
 *     @OA\Property(
 *         property="email_me",
 *         type="boolean",
 *         enum={0,1}
 *     ),
 *     @OA\Property(
 *         property="additional_emails",
 *         type="array",
 *         @OA\Items(type="string")
 *     ),
 *     @OA\Property(
 *         property="phone_numbers",
 *         type="array",
 *         @OA\Items(type="string")
 *     ),
 *     @OA\Property(
 *         property="idSites",
 *         description="IDs of the sites",
 *         type="array",
 *         @OA\Items(type="integer")
 *     )
 * )
 *
 * @OA\Parameter(
 *     parameter="idAlert",
 *     name="idAlert",
 *     in="query",
 *     description="The ID of the alert",
 *     required=true,
 *     @OA\Schema(
 *         type="integer"
 *     )
 * )
 * @OA\Parameter(
 *     parameter="alertName",
 *     name="name",
 *     in="query",
 *     description="The name to identify alert",
 *     required=true,
 *     @OA\Schema(type="string")
 * )
 * @OA\Parameter(
 *     parameter="alertIdSites",
 *     name="idSites",
 *     in="query",
 *     description="The IDs of the sites for which an alert is configured",
 *     required=true,
 *     @OA\Schema(
 *         oneOf={
 *             @OA\Schema(type="string"),
 *             @OA\Schema(
 *                 type="array",
 *                 @OA\Items(
 *                     oneOf={
 *                         @OA\Schema(type="integer"),
 *                         @OA\Schema(type="string")
 *                     }
 *                 )
 *             )
 *         }
 *     )
 * )
 * @OA\Parameter(
 *     parameter="alertPeriod",
 *     name="period",
 *     in="query",
 *     required=true,
 *     @OA\Schema(type="string")
 * )
 * @OA\Parameter(
 *     parameter="alertEmailMe",
 *     name="emailMe",
 *     in="query",
 *     required=true,
 *     @OA\Schema(type="boolean")
 * )
 * @OA\Parameter(
 *     parameter="alertAdditionalEmails",
 *     name="additionalEmails",
 *     in="query",
 *     required=true,
 *     @OA\Schema(
 *         type="array",
 *         minItems=0,
 *         @OA\Items(type="string")
 *     )
 * )
 * @OA\Parameter(
 *     parameter="alertPhoneNumbers",
 *     name="phoneNumbers",
 *     in="query",
 *     required=true,
 *     @OA\Schema(
 *         type="array",
 *         minItems=0,
 *         @OA\Items(type="string")
 *     )
 * )
 * @OA\Parameter(
 *     parameter="alertMetric",
 *     name="metric",
 *     in="query",
 *     required=true,
 *     @OA\Schema(type="string")
 * )
 * @OA\Parameter(
 *     parameter="alertMetricCondition",
 *     name="metricCondition",
 *     in="query",
 *     required=true,
 *     @OA\Schema(type="string")
 * )
 * @OA\Parameter(
 *     parameter="alertMetricValue",
 *     name="metricValue",
 *     in="query",
 *     required=true,
 *     @OA\Schema(type="number")
 * )
 * @OA\Parameter(
 *     parameter="alertComparedTo",
 *     name="comparedTo",
 *     in="query",
 *     required=true,
 *     @OA\Schema(type="integer")
 * )
 * @OA\Parameter(
 *     parameter="alertReportUniqueId",
 *     name="reportUniqueId",
 *     in="query",
 *     required=true,
 *     @OA\Schema(type="string")
 * )
 * @OA\Parameter(
 *     parameter="alertReportCondition",
 *     name="reportCondition",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string")
 * )
 * @OA\Parameter(
 *     parameter="alertReportValue",
 *     name="reportValue",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string")
 * )
 */
class API extends \Piwik\Plugin\API
{
    private $validator;
    /**
     * @var Processor
     */
    private $processor;

    public function __construct(Processor $processor, Validator $validator)
    {
        $this->validator = $validator;
        $this->processor = $processor;
    }

    /**
     * Calculates the alert value for each site for the given days/weeks/months in past. If the period of the alert is
     * weeks and subPeriodN is "7" it will return the value for the week 7 weeks ago. Set subPeriodN to "0" to test the
     * current day/week/month.
     *
     * @param int $idAlert
     * @param int $subPeriodN
     *
     * @return array
     *
     * @OA\Get(
     *     path="/index.php?method=CustomAlerts.getValuesForAlertInPast",
     *     operationId="CustomAlerts.getValuesForAlertInPast",
     *     tags={"CustomAlerts"},
     *     @OA\Parameter(ref="#/components/parameters/module"),
     *     @OA\Parameter(ref="#/components/parameters/format"),
     *     @OA\Parameter(ref="#/components/parameters/idAlert"),
     *     @OA\Parameter(
     *         name="subPeriodN",
     *         in="query",
     *         description="The number to subtract from the current (0) period",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="The collection of sites their the matching result for the specified period",
     *         @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(
     *                      property="idSite",
     *                      description="ID of the site",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="value",
     *                      description="Value of the alert for specific site",
     *                      type="array",
     *                      @OA\Items(
     *                          oneOf={
     *                              @OA\Schema(type="string"),
     *                              @OA\Schema(type="integer"),
     *                              @OA\Schema(type="object")
     *                          }
     *                      )
     *                  )
     *              )
     *         )
     *     )
     * )
     */
    public function getValuesForAlertInPast($idAlert, $subPeriodN)
    {
        $alert = $this->getAlert($idAlert);

        $values = [];
        foreach ($alert['id_sites'] as $idSite) {
            $values[] = array(
                'idSite' => (int)$idSite,
                'value'  => $this->processor->getValueForAlertInPast($alert, $idSite, (int)$subPeriodN)
            );
        }

        return $values;
    }

    /**
     * Returns a single alert.
     *
     * @param int $idAlert
     *
     * @return array
     * @throws \Exception In case alert does not exist or user has no permission to access alert.
     *
     * @OA\Get(
     *     path="/index.php?method=CustomAlerts.getAlert",
     *     operationId="CustomAlerts.getAlert",
     *     tags={"CustomAlerts"},
     *     @OA\Parameter(ref="#/components/parameters/module"),
     *     @OA\Parameter(ref="#/components/parameters/format"),
     *     @OA\Parameter(ref="#/components/parameters/idAlert"),
     *     @OA\Response(
     *         response="200",
     *         description="The properties of the requested alert",
     *         @OA\JsonContent(ref="#/components/schemas/alert")
     *     )
     * )
     */
    public function getAlert($idAlert)
    {
        $alert = $this->getModel()->getAlert($idAlert);

        if (empty($alert)) {
            throw new Exception(Piwik::translate('CustomAlerts_AlertDoesNotExist', $idAlert));
        }

        $this->validator->checkUserHasPermissionForAlert($alert);

        return $alert;
    }

    private function getModel()
    {
        return new Model();
    }

    /**
     * Returns the Alerts that are defined on the idSites given.
     *
     * @param array $idSites
     * @param bool  $ifSuperUserReturnAllAlerts
     *
     * @return array
     *
     * @OA\Get(
     *     path="/index.php?method=CustomAlerts.getAlerts",
     *     operationId="CustomAlerts.getAlerts",
     *     tags={"CustomAlerts"},
     *     @OA\Parameter(ref="#/components/parameters/module"),
     *     @OA\Parameter(ref="#/components/parameters/format"),
     *     @OA\Parameter(ref="#/components/parameters/alertIdSites"),
     *     @OA\Response(
     *         response="200",
     *         description="A collection of alerts matching the sites",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/alert")
     *         )
     *     )
     * )
     */
    public function getAlerts($idSites, $ifSuperUserReturnAllAlerts = false)
    {
        $idSites = Site::getIdSitesFromIdSitesString($idSites);

        if (empty($idSites)) {
            return [];
        }

        Piwik::checkUserHasViewAccess($idSites);

        if (Piwik::hasUserSuperUserAccess() && $ifSuperUserReturnAllAlerts) {
            $login = false;
        } else {
            $login = Piwik::getCurrentUserLogin();
        }

        $alerts = $this->getModel()->getAlerts($idSites, $login);

        return $alerts;
    }

    /**
     * Creates an Alert for given website(s).
     *
     * @param string      $name
     * @param mixed       $idSites
     * @param string      $period
     * @param bool        $emailMe
     * @param array       $additionalEmails
     * @param array       $phoneNumbers
     * @param string      $metric (nb_uniq_visits, sum_visit_length, ..)
     * @param string      $metricCondition
     * @param float       $metricValue
     * @param string      $reportUniqueId
     * @param int         $comparedTo
     * @param bool|string $reportCondition
     * @param bool|string $reportValue
     * @return int ID of new Alert
     *
     * @OA\Post(
     *     path="/index.php?method=CustomAlerts.addAlert",
     *     operationId="CustomAlerts.addAlert",
     *     tags={"CustomAlerts"},
     *     @OA\Parameter(ref="#/components/parameters/module"),
     *     @OA\Parameter(ref="#/components/parameters/format"),
     *     @OA\Parameter(ref="#/components/parameters/alertName"),
     *     @OA\Parameter(ref="#/components/parameters/alertIdSites"),
     *     @OA\Parameter(ref="#/components/parameters/alertPeriod"),
     *     @OA\Parameter(ref="#/components/parameters/alertEmailMe"),
     *     @OA\Parameter(ref="#/components/parameters/alertAdditionalEmails"),
     *     @OA\Parameter(ref="#/components/parameters/alertPhoneNumbers"),
     *     @OA\Parameter(ref="#/components/parameters/alertMetric"),
     *     @OA\Parameter(ref="#/components/parameters/alertMetricCondition"),
     *     @OA\Parameter(ref="#/components/parameters/alertMetricValue"),
     *     @OA\Parameter(ref="#/components/parameters/alertComparedTo"),
     *     @OA\Parameter(ref="#/components/parameters/alertReportUniqueId"),
     *     @OA\Parameter(ref="#/components/parameters/alertReportCondition"),
     *     @OA\Parameter(ref="#/components/parameters/alertReportValue"),
     *     @OA\Response(
     *         response="200",
     *         description="ID of the newly created alert",
     *         @OA\JsonContent(type="integer")
     *     )
     * )
     */
    public function addAlert($name, $idSites, $period, $emailMe, $additionalEmails, $phoneNumbers, $metric, $metricCondition, $metricValue, $comparedTo, $reportUniqueId, $reportCondition = false, $reportValue = false)
    {
        $idSites          = Site::getIdSitesFromIdSitesString($idSites);
        $additionalEmails = $this->filterAdditionalEmails($additionalEmails);
        $phoneNumbers     = $this->filterPhoneNumbers($phoneNumbers);

        $this->checkAlert($idSites, $name, $period, $additionalEmails, $metricCondition, $metric, $comparedTo, $reportCondition, $reportUniqueId);

        $name  = Common::unsanitizeInputValue($name);
        $login = Piwik::getCurrentUserLogin();

        if (empty($reportCondition) || empty($reportValue)) {
            $reportCondition = null;
            $reportValue     = null;
        }

        $metricValue = Common::forceDotAsSeparatorForDecimalPoint((float)$metricValue);

        return $this->getModel()->createAlert($name, $idSites, $login, $period, $emailMe, $additionalEmails, $phoneNumbers, $metric, $metricCondition, $metricValue, $comparedTo, $reportUniqueId, $reportCondition, $reportValue);
    }

    private function filterAdditionalEmails($additionalEmails)
    {
        if (empty($additionalEmails)) {
            return array();
        }

        foreach ($additionalEmails as &$email) {
            $email = trim($email);
            if (empty($email)) {
                $email = false;
            }
        }

        return array_filter($additionalEmails);
    }

    private function filterPhoneNumbers($phoneNumbers)
    {
        $availablePhoneNumbers = (new \Piwik\Plugins\MobileMessaging\Model())->getActivatedPhoneNumbers(Piwik::getCurrentUserLogin());

        foreach ($phoneNumbers as $key => &$phoneNumber) {
            $phoneNumber = trim($phoneNumber);

            if (!in_array($phoneNumber, $availablePhoneNumbers)) {
                unset($phoneNumbers[$key]);
            }
        }

        return array_values($phoneNumbers);
    }

    private function checkAlert($idSites, $name, $period, $additionalEmails, $metricCondition, $metricValue, $comparedTo, $reportCondition, $reportUniqueId)
    {
        Piwik::checkUserHasViewAccess($idSites);

        $this->validator->checkName($name);
        $this->validator->checkPeriod($period);
        $this->validator->checkComparedTo($period, $comparedTo);
        $this->validator->checkMetricCondition($metricCondition);
        $this->validator->checkReportCondition($reportCondition);

        foreach ($idSites as $idSite) {
            $this->validator->checkApiMethodAndMetric($idSite, $reportUniqueId, $metricValue);
        }

        $this->validator->checkAdditionalEmails($additionalEmails);
    }

    /**
     * Edits an Alert for given website(s).
     *
     * @param             $idAlert
     * @param string      $name    Name of Alert
     * @param mixed       $idSites Single int or array of ints of idSites.
     * @param string      $period  Period the alert is defined on.
     * @param bool        $emailMe
     * @param array       $additionalEmails
     * @param array       $phoneNumbers
     * @param string      $metric  (nb_uniq_visits, sum_visit_length, ..)
     * @param string      $metricCondition
     * @param float       $metricValue
     * @param string      $reportUniqueId
     * @param int         $comparedTo
     * @param bool|string $reportCondition
     * @param bool|string $reportValue
     *
     * @return boolean
     *
     * @OA\Post(
     *      path="/index.php?method=CustomAlerts.editAlert",
     *      operationId="CustomAlerts.editAlert",
     *      tags={"CustomAlerts"},
     *      @OA\Parameter(ref="#/components/parameters/module"),
     *      @OA\Parameter(ref="#/components/parameters/format"),
     *      @OA\Parameter(ref="#/components/parameters/idAlert"),
     *      @OA\Parameter(ref="#/components/parameters/alertName"),
     *      @OA\Parameter(ref="#/components/parameters/alertIdSites"),
     *      @OA\Parameter(ref="#/components/parameters/alertPeriod"),
     *      @OA\Parameter(ref="#/components/parameters/alertEmailMe"),
     *      @OA\Parameter(ref="#/components/parameters/alertAdditionalEmails"),
     *      @OA\Parameter(ref="#/components/parameters/alertPhoneNumbers"),
     *      @OA\Parameter(ref="#/components/parameters/alertMetric"),
     *      @OA\Parameter(ref="#/components/parameters/alertMetricCondition"),
     *      @OA\Parameter(ref="#/components/parameters/alertMetricValue"),
     *      @OA\Parameter(ref="#/components/parameters/alertComparedTo"),
     *      @OA\Parameter(ref="#/components/parameters/alertReportUniqueId"),
     *      @OA\Parameter(ref="#/components/parameters/alertReportCondition"),
     *      @OA\Parameter(ref="#/components/parameters/alertReportValue"),
     *      @OA\Response(
     *          response="200",
     *          description="Boolean indicating success",
     *          @OA\JsonContent(type="boolean")
     *      )
     *  )
     */
    public function editAlert($idAlert, $name, $idSites, $period, $emailMe, $additionalEmails, $phoneNumbers, $metric, $metricCondition, $metricValue, $comparedTo, $reportUniqueId, $reportCondition = false, $reportValue = false)
    {
        // make sure alert exists and user has permission to read
        $this->getAlert($idAlert);

        $idSites          = Site::getIdSitesFromIdSitesString($idSites);
        $additionalEmails = $this->filterAdditionalEmails($additionalEmails);
        $phoneNumbers     = $this->filterPhoneNumbers($phoneNumbers);

        $this->checkAlert($idSites, $name, $period, $additionalEmails, $metricCondition, $metric, $comparedTo, $reportCondition, $reportUniqueId);

        $name = Common::unsanitizeInputValue($name);

        if (empty($reportCondition) || empty($reportValue)) {
            $reportCondition = null;
            $reportValue     = null;
        }

        $metricValue = Common::forceDotAsSeparatorForDecimalPoint((float)$metricValue);

        return $this->getModel()->updateAlert($idAlert, $name, $idSites, $period, $emailMe, $additionalEmails, $phoneNumbers, $metric, $metricCondition, $metricValue, $comparedTo, $reportUniqueId, $reportCondition, $reportValue);
    }

    /**
     * Delete alert by id.
     *
     * @param int $idAlert
     * @throws \Exception
     *
     * @OA\Delete(
     *     path="/index.php?method=CustomAlerts.deleteAlert",
     *     operationId="CustomAlerts.deleteAlert",
     *     tags={"CustomAlerts"},
     *     @OA\Parameter(ref="#/components/parameters/module"),
     *     @OA\Parameter(ref="#/components/parameters/format"),
     *     @OA\Parameter(
     *         name="idAlert",
     *         in="query",
     *         description="The ID of the alert",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="200 response without body"
     *     )
     * )
     */
    public function deleteAlert($idAlert)
    {
        // make sure alert exists and user has permission to read
        $this->getAlert($idAlert);

        $this->getModel()->deleteAlert($idAlert);
    }

    /**
     * Get triggered alerts.
     *
     * @param int[] idSites
     *
     * @return array
     *
     * @OA\Get(
     *     path="/index.php?method=CustomAlerts.getTriggeredAlerts",
     *     operationId="CustomAlerts.getTriggeredAlerts",
     *     tags={"CustomAlerts"},
     *     @OA\Parameter(ref="#/components/parameters/module"),
     *     @OA\Parameter(ref="#/components/parameters/format"),
     *     @OA\Parameter(
     *         name="idSites",
     *         in="query",
     *         description="The IDs of the sites to filter alerts by",
     *         required=true,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="A collection of alerts matching the sites",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/alert")
     *         )
     *     )
     * )
     */
    public function getTriggeredAlerts($idSites)
    {
        if (empty($idSites)) {
            return array();
        }

        $idSites = Site::getIdSitesFromIdSitesString($idSites);
        Piwik::checkUserHasViewAccess($idSites);

        $login = Piwik::getCurrentUserLogin();

        return $this->getModel()->getTriggeredAlerts($idSites, $login);
    }
}
