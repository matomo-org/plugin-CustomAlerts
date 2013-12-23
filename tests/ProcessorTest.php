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

class CustomProcessor extends Processor {
    public function filterDataTable($dataTable, $condition, $value) {
        parent::filterDataTable($dataTable, $condition, $value);
    }

    public function getMetricFromTable($dataTable, $metric, $filterCond, $filterValue)
    {
        return parent::getMetricFromTable($dataTable, $metric, $filterCond, $filterValue);
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
class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CustomProcessor
     */
    private $processor;

    public function setUp()
    {
        parent::setUp();

        $this->processor = new CustomProcessor();
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

}