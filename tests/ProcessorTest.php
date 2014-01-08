<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\CustomAlerts\tests;

use Piwik\DataTable;
use Piwik\DataTable\Row;
use Piwik\Plugins\CustomAlerts\Processor;
use Piwik\Translate;
use Piwik\Access;

class CustomProcessor extends Processor {
    public function filterDataTable($dataTable, $condition, $value) {
        parent::filterDataTable($dataTable, $condition, $value);
    }

    public function getMetricFromTable($dataTable, $metric, $filterCond = '', $filterValue = '')
    {
        return parent::getMetricFromTable($dataTable, $metric, $filterCond, $filterValue);
    }

    public function processAlert($alert)
    {
        parent::processAlert($alert);
    }

    public function shouldBeTriggered($alert, $metricOne, $metricTwo)
    {
        return parent::shouldBeTriggered($alert, $metricOne, $metricTwo);
    }
}

/**
 * @group CustomAlerts
 * @group ProcessorTest
 * @group Unit
 */
class ProcessorTest extends \DatabaseTestCase
{
    /**
     * @var CustomProcessor
     */
    private $processor;

    public function setUp()
    {
        parent::setUp();

        $this->processor = new CustomProcessor();

        $this->setSuperUser();
        \Test_Piwik_BaseFixture::createWebsite('2012-08-09 11:22:33');
        \Test_Piwik_BaseFixture::createWebsite('2012-08-07 11:22:33');
    }

    private function getDataTable()
    {
        $dataTable = new DataTable();
        $dataTable->addRowsFromArray(array(
                array(Row::COLUMNS => array('label' => 'ten', 'visits' => 10)),
                array(Row::COLUMNS => array('label' => '3test', 'visits' => 33)),
                array(Row::COLUMNS => array('label' => 'ninety', 'visits' => 90)),
                array(Row::COLUMNS => array('label' => '3test', 'visits' => 65)),
                array(Row::COLUMNS => array('label' => '3teste', 'visits' => 67)),
                array(Row::COLUMNS => array('label' => 'hundred', 'visits' => 100))
        ));

        return $dataTable;
    }

    public function test_isValidGroupCondition()
    {
        $this->assertFalse(Processor::isValidGroupCondition(null));
        $this->assertFalse(Processor::isValidGroupCondition(''));
        $this->assertFalse(Processor::isValidGroupCondition('matchesany'));

        $this->assertTrue(Processor::isValidGroupCondition('matches_any'));
        $this->assertTrue(Processor::isValidGroupCondition('matches_exactly'));
    }

    public function test_isValidMetricCondition()
    {
        $this->assertFalse(Processor::isValidMetricCondition(null));
        $this->assertFalse(Processor::isValidMetricCondition(''));
        $this->assertFalse(Processor::isValidMetricCondition('lessthan'));

        $this->assertTrue(Processor::isValidMetricCondition('less_than'));
        $this->assertTrue(Processor::isValidMetricCondition('greater_than'));
    }

    public function test_isValidComparableDate()
    {
        $this->assertFalse(Processor::isValidComparableDate('invalid', 1));
        $this->assertFalse(Processor::isValidComparableDate('', 12));
        $this->assertFalse(Processor::isValidComparableDate('day', 88));

        $this->assertTrue(Processor::isValidComparableDate('day', 1));
        $this->assertTrue(Processor::isValidComparableDate('month', 12));
    }

    public function test_filterDataTable_Condition_MatchesAny()
    {
        $this->assertFilterResult('matches_any', '3test', array(10, 33, 90, 65, 67, 100));
    }

    public function test_filterDataTable_Condition_MatchesExactly()
    {
        $this->assertFilterResult('matches_exactly', '3test', array(33, 65));
        $this->assertFilterResult('matches_exactly', 'ninety', array(90));
        $this->assertFilterResult('matches_exactly', 'NoneMatChIng', array());
    }

    public function test_filterDataTable_Condition_DoesNotMatchExactly()
    {
        $this->assertFilterResult('does_not_match_exactly', '3test', array(10, 90, 67, 100));
        $this->assertFilterResult('does_not_match_exactly', 'ninety', array(10, 33, 65, 67, 100));
        $this->assertFilterResult('does_not_match_exactly', 'AllMatChIng', array(10, 33, 90, 65, 67, 100));
    }

    public function test_filterDataTable_Condition_MatchesRegex()
    {
        $this->assertFilterResult('matches_regex', '3test', array(33, 65, 67));
        $this->assertFilterResult('matches_regex', '3te', array(33, 65, 67));
        $this->assertFilterResult('matches_regex', '(.*t)', array(10, 33, 90, 65, 67));
    }

    public function test_filterDataTable_Condition_DoesNotMatchRegex()
    {
        $this->assertFilterResult('does_not_match_regex', '3test', array(10, 90, 100));
        $this->assertFilterResult('does_not_match_regex', '3te', array(10, 90, 100));
        $this->assertFilterResult('does_not_match_regex', '(.*t)', array(100));
    }

    public function test_filterDataTable_Condition_Contains()
    {
        $this->assertFilterResult('contains', '3test', array(33, 65, 67));
        $this->assertFilterResult('contains', '3te', array(33, 65, 67));
        $this->assertFilterResult('contains', 'ninety', array(90));
        $this->assertFilterResult('contains', 'NoneMatChIng', array());
    }

    public function test_filterDataTable_Condition_DoesNotContain()
    {
        $this->assertFilterResult('does_not_contain', '3test', array(10, 90, 100));
        $this->assertFilterResult('does_not_contain', '3te', array(10, 90, 100));
        $this->assertFilterResult('does_not_contain', 'ninety', array(10, 33, 65, 67, 100));
        $this->assertFilterResult('does_not_contain', 'AllMatching', array(10, 33, 90, 65, 67, 100));
    }

    public function test_filterDataTable_Condition_StartsWith()
    {
        $this->assertFilterResult('starts_with', '3test', array(33, 65, 67));
        $this->assertFilterResult('starts_with', '3te', array(33, 65, 67));
        $this->assertFilterResult('starts_with', 'ninety', array(90));
        $this->assertFilterResult('starts_with', 't', array(10));
        $this->assertFilterResult('starts_with', 'NoneMatChIng', array());
    }

    public function test_filterDataTable_Condition_DoesNotStartWith()
    {
        $this->assertFilterResult('does_not_start_with', '3test', array(10, 90, 100));
        $this->assertFilterResult('does_not_start_with', '3te', array(10, 90, 100));
        $this->assertFilterResult('does_not_start_with', 'ninety', array(10, 33, 65, 67, 100));
        $this->assertFilterResult('does_not_start_with', 't', array(33, 90, 65, 67, 100));
        $this->assertFilterResult('does_not_start_with', 'AllMatchIng', array(10, 33, 90, 65, 67, 100));
    }

    public function test_filterDataTable_Condition_EndsWith()
    {
        $this->assertFilterResult('ends_with', 't', array(33, 65));
        $this->assertFilterResult('ends_with', 'n', array(10));
        $this->assertFilterResult('ends_with', 'ninety', array(90));
        $this->assertFilterResult('ends_with', 'NoneMatChIng', array());
    }

    public function test_filterDataTable_Condition_DoesNotEndWith()
    {
        $this->assertFilterResult('does_not_end_with', 't', array(10, 90, 67, 100));
        $this->assertFilterResult('does_not_end_with', 'n', array(33, 90, 65, 67, 100));
        $this->assertFilterResult('does_not_end_with', 'ninety', array(10, 33, 65, 67, 100));
        $this->assertFilterResult('does_not_end_with', 'NoneMatChIng', array(10, 33, 90, 65, 67, 100));
    }

    /**
     * @expectedException \Exception
     */
    public function test_filterDataTable_shouldThrowException_IfConditionIsInvalid()
    {
        $this->assertFilterResult('noTValIdConDitiOn', 't', array());
    }

    public function test_getMetricFromTable()
    {
        $this->assertMetricFromTable('visits', '', '', 365);
        $this->assertMetricFromTable('visits', 'contains', '3test', 165);
        $this->assertMetricFromTable('visits', 'matches_exactly', 'ten', 10);
        $this->assertMetricFromTable('visits', 'matches_exactly', 'NonE', null);
    }

    public function test_getMetricFromTable_invalidMetric()
    {
        $this->assertMetricFromTable('NotValidMeTriC', '', '', null);
    }

    public function test_shouldBeTriggered_GreaterThan()
    {
        $this->assertShouldBeTriggered('greater_than', 20, 30, null);
        $this->assertShouldBeTriggered('greater_than', 20, 30, 30);
        $this->assertShouldBeTriggered('greater_than', 20, 30, 15);

        $this->assertShouldNotBeTriggered('greater_than', 20, 10, null);
        $this->assertShouldNotBeTriggered('greater_than', 20, 10, 30);
        $this->assertShouldNotBeTriggered('greater_than', 20, 10, 15);
    }

    public function test_shouldBeTriggered_LessThan()
    {
        $this->assertShouldBeTriggered('less_than', 20, 10, null);
        $this->assertShouldBeTriggered('less_than', 20, 10, 15);
        $this->assertShouldBeTriggered('less_than', 20, 10, 30);

        $this->assertShouldNotBeTriggered('less_than', 20, 30, null);
        $this->assertShouldNotBeTriggered('less_than', 20, 30, 30);
        $this->assertShouldNotBeTriggered('less_than', 20, 30, 15);
    }

    public function test_shouldBeTriggered_DecreaseMoreThan()
    {
        $this->assertShouldBeTriggered('decrease_more_than', 29, 70, 100);
        $this->assertShouldBeTriggered('decrease_more_than', 1, 70, 200);
        $this->assertShouldBeTriggered('decrease_more_than', 1, null, 200);

        $this->assertShouldNotBeTriggered('decrease_more_than', 30, 70, 100);
        $this->assertShouldNotBeTriggered('decrease_more_than', 31, 70, 100);
        $this->assertShouldNotBeTriggered('decrease_more_than', 29, 70, null);
        $this->assertShouldNotBeTriggered('decrease_more_than', 29, 70, 70);
        $this->assertShouldNotBeTriggered('decrease_more_than', 29, 100, 70);
        $this->assertShouldNotBeTriggered('decrease_more_than', 31, 100, 70);
    }

    public function test_shouldBeTriggered_IncreaseMoreThan()
    {
        $this->assertShouldBeTriggered('increase_more_than', 29, 100, 70);
        $this->assertShouldBeTriggered('increase_more_than', 1, 200, 70);
        $this->assertShouldBeTriggered('increase_more_than', 1, 200, null);

        $this->assertShouldNotBeTriggered('increase_more_than', 30, 100, 70);
        $this->assertShouldNotBeTriggered('increase_more_than', 31, 100, 70);
        $this->assertShouldNotBeTriggered('increase_more_than', 29, null, 70);
        $this->assertShouldNotBeTriggered('increase_more_than', 29, 70, 70);
        $this->assertShouldNotBeTriggered('increase_more_than', 29, 70, 100);
        $this->assertShouldNotBeTriggered('increase_more_than', 31, 70, 100);
    }

    public function test_shouldBeTriggered_PercentageDecreaseMoreThan()
    {
        $this->assertShouldBeTriggered('percentage_decrease_more_than', 15, 70, 100);
        $this->assertShouldBeTriggered('percentage_decrease_more_than', 29, 70, 100);
        $this->assertShouldBeTriggered('percentage_decrease_more_than', 29, null, 30);

        $this->assertShouldNotBeTriggered('percentage_decrease_more_than', 30, 70, 100);
        $this->assertShouldNotBeTriggered('percentage_decrease_more_than', 31, 70, 100);
        $this->assertShouldNotBeTriggered('percentage_decrease_more_than', 31, 100, 70);
        $this->assertShouldNotBeTriggered('percentage_decrease_more_than', 31, null, null);
    }

    public function test_shouldBeTriggered_PercentageIncreaseMoreThan()
    {
        $this->assertShouldBeTriggered('percentage_increase_more_than', 30, 100, 70);
        $this->assertShouldBeTriggered('percentage_increase_more_than', 41, 100, 70);
        $this->assertShouldBeTriggered('percentage_increase_more_than', 42, 100, 70);
        $this->assertShouldBeTriggered('percentage_increase_more_than', 43, 44, null);

        $this->assertShouldNotBeTriggered('percentage_increase_more_than', 43, 100, 70);
        $this->assertShouldNotBeTriggered('percentage_increase_more_than', 43, null, null);
    }

    public function test_processAlert_shouldTriggerAlertAndRunForEachDefinedWebsite()
    {
        $alert = array(
            'idalert' => 1,
            'period'  => 'month',
            'idSites' => array(1, 2),
            'metric_condition' => 'increase_more_than',
            'metric_matched'   => '4',
            'report' => 'MultiSites.getAll',
            'metric' => 'nb_visits',
            'compared_to' => 12
        );

        $methods = array('getValueForAlertInPast', 'triggerAlert');
        $processorMock = $this->getMock('Piwik\Plugins\CustomAlerts\tests\CustomProcessor', $methods);
        $processorMock->expects($this->at(0))
                      ->method('getValueForAlertInPast')
                      ->with($this->equalTo($alert), $this->equalTo(1), $this->equalTo(1))
                      ->will($this->returnValue(13));

        $processorMock->expects($this->at(1))
                      ->method('getValueForAlertInPast')
                      ->with($this->equalTo($alert), $this->equalTo(1), $this->equalTo(13))
                      ->will($this->returnValue(10));

        $processorMock->expects($this->at(2))
                      ->method('getValueForAlertInPast')
                      ->with($this->equalTo($alert), $this->equalTo(2), $this->equalTo(1))
                      ->will($this->returnValue(15));

        $processorMock->expects($this->at(3))
                      ->method('getValueForAlertInPast')
                      ->with($this->equalTo($alert), $this->equalTo(2), $this->equalTo(13))
                      ->will($this->returnValue(10));

        $processorMock->expects($this->once())
                      ->method('triggerAlert')
                      ->with($this->equalTo($alert), $this->equalTo(2), $this->equalTo(15), $this->equalTo(10));

        $processorMock->processAlert($alert);
    }

    public function test_processAlert_shouldNotFail_IfReportDoesNotExist()
    {
        $alert = array(
            'idalert' => 1,
            'period'  => 'week',
            'idSites' => array(1, 2),
            'metric_condition' => 'increase_more_than',
            'metric_matched'   => '4',
            'report' => 'NotExistingReport.Action',
            'metric' => 'nb_visits',
            'compared_to' => '1'
        );

        $methods = array('getValueForAlertInPast', 'triggerAlert');
        $processorMock = $this->getMock('Piwik\Plugins\CustomAlerts\tests\CustomProcessor', $methods);
        $processorMock->expects($this->never())
                      ->method('getValueForAlertInPast');

        $processorMock->expects($this->never())
                      ->method('triggerAlert');

        $processorMock->processAlert($alert);
    }

    public function test_processAlert_shouldNotFail_IfMetricDoesNotBelongToTheReport()
    {
        $alert = array(
            'idalert' => 1,
            'period'  => 'week',
            'idSites' => array(1, 2),
            'metric_condition' => 'increase_more_than',
            'metric_matched'   => '4',
            'report' => 'MultiSites.getAll',
            'metric' => 'not_existing_metric',
            'compared_to' => 1
        );

        $methods = array('getValueForAlertInPast', 'triggerAlert');
        $processorMock = $this->getMock('Piwik\Plugins\CustomAlerts\tests\CustomProcessor', $methods);
        $processorMock->expects($this->never())
                      ->method('getValueForAlertInPast');

        $processorMock->expects($this->never())
                      ->method('triggerAlert');

        $processorMock->processAlert($alert);
    }

    public function test_processAlert_shouldNotRun_IfNoWebsitesDefined()
    {
        $alert = array(
            'idalert' => 1,
            'period'  => 'week',
            'idSites' => array(),
            'metric_condition' => 'increase_more_than',
            'metric_matched'   => '4',
            'compared_to' => 1
        );

        $methods = array('getValueForAlertInPast', 'triggerAlert');
        $processorMock = $this->getMock('Piwik\Plugins\CustomAlerts\tests\CustomProcessor', $methods);
        $processorMock->expects($this->never())
                      ->method('getValueForAlertInPast');

        $processorMock->expects($this->never())
                      ->method('triggerAlert');

        $processorMock->processAlert($alert);
    }

    public function test_processAlert_shouldOnlyBeTriggeredIfAlertMatches()
    {
        $alert = array(
            'idalert' => 1,
            'period'  => 'day',
            'idSites'  => array(1),
            'metric_condition' => 'increase_more_than',
            'metric_matched'   => '5',
            'report' => 'MultiSites.getAll',
            'metric' => 'nb_visits',
            'compared_to' => 7
        );

        $methods = array('getValueForAlertInPast', 'triggerAlert');
        $processorMock = $this->getMock('Piwik\Plugins\CustomAlerts\tests\CustomProcessor', $methods);
        $processorMock->expects($this->at(0))
                      ->method('getValueForAlertInPast')
                      ->with($this->equalTo($alert), $this->equalTo(1), $this->equalTo(1))
                      ->will($this->returnValue(15));

        $processorMock->expects($this->at(1))
                      ->method('getValueForAlertInPast')
                      ->with($this->equalTo($alert), $this->equalTo(1), $this->equalTo(8))
                      ->will($this->returnValue(10));

        $processorMock->expects($this->never())
                      ->method('triggerAlert');

        $processorMock->processAlert($alert);
    }

    /**
     * @expectedException \Exception
     */
    public function test_shouldBeTriggered_ShouldFail_IfInvalidConditionGiven()
    {
        $this->assertShouldBeTriggered('NotExistInG', 30, 100, 70);
    }

    private function assertShouldBeTriggered($metricCondition, $metricMatched, $metricPast1, $metricPast2)
    {
        $result = $this->shouldBeTriggered($metricCondition, $metricMatched, $metricPast1, $metricPast2);

        $this->assertTrue($result);
    }

    private function assertShouldNotBeTriggered($metricCondition, $metricMatched, $metricPast1, $metricPast2)
    {
        $result = $this->shouldBeTriggered($metricCondition, $metricMatched, $metricPast1, $metricPast2);

        $this->assertFalse($result);
    }

    private function assertFilterResult($condition, $filterValue, $resultedVisits)
    {
        $dataTable = $this->getDataTable();

        $this->processor->filterDataTable($dataTable, $condition, $filterValue);

        $this->assertEquals(count($resultedVisits), $dataTable->getRowsCount());

        $rows = $dataTable->getRows();
        foreach ($resultedVisits as $resultedVisit) {
            $row = array_shift($rows);
            $this->assertEquals($resultedVisit, $row->getColumn('visits'));
        }
    }

    private function assertMetricFromTable($metric, $filterCondition, $filterValue, $result)
    {
        $dataTable = $this->getDataTable();

        $metric = $this->processor->getMetricFromTable($dataTable, $metric, $filterCondition, $filterValue);

        $this->assertEquals($result, $metric);
    }

    private function shouldBeTriggered($metricCondition, $metricMatched, $metricPast1, $metricPast2)
    {
        $alert = array(
            'metric_condition' => $metricCondition,
            'metric_matched'   => $metricMatched
        );

        return $this->processor->shouldBeTriggered($alert, $metricPast1, $metricPast2);
    }

    private function setSuperUser()
    {
        $pseudoMockAccess = new \FakeAccess();
        \FakeAccess::$superUser = true;
        Access::setSingletonInstance($pseudoMockAccess);
    }

}