<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\CustomAlerts\tests;

use Piwik\Plugins\CustomAlerts\Validator;
use Piwik\Translate;

/**
 * @group CustomAlerts
 * @group ModelTest
 * @group Database
 */
class ValidatorTest extends BaseTest
{
    /**
     * @var \Piwik\Plugins\CustomAlerts\Validator
     */
    private $validator;

    public function setUp()
    {
        parent::setUp();

        $this->validator = new Validator();
        Translate::unloadEnglishTranslation();
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage UsersManager_ExceptionInvalidEmail
     */
    public function test_checkAdditionalEmails_ShouldFail_IfContainsInvalidEmail()
    {
        $this->validator->checkAdditionalEmails(array('test@example.com', 'invalidemail'));
    }

    public function test_checkAdditionalEmails_ShouldNotFail_IfAllEmail()
    {
        $this->assertNull($this->validator->checkAdditionalEmails(array('test@example.com', 'test@example.com')));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage CustomAlerts_AccessException
     */
    public function test_checkUserHasPermissionForAlert_ShouldFail_IfInvalid()
    {
        $this->setUser();

        $alert = array(
            'idalert' => 5,
            'login'   => 'WhatEver'
        );

        $this->validator->checkUserHasPermissionForAlert($alert);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage CustomAlerts_AccessException
     */
    public function test_checkUserHasPermissionForAlert_ShouldFail_IfInvalidEventIfUserIsSuperUser()
    {
        $this->setSuperUser();

        $alert = array(
            'idalert' => 5,
            'login'   => 'WhatEver'
        );

        $this->validator->checkUserHasPermissionForAlert($alert);
    }

    public function test_checkUserHasPermissionForAlert_ShouldNotFail_IfValid()
    {
        $this->setUser();

        $alert = array(
            'idalert' => 5,
            'login'   => 'aUser'
        );

        $this->assertNull($this->validator->checkUserHasPermissionForAlert($alert));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage CustomAlerts_InvalidPeriod
     */
    public function test_checkPeriod_ShouldFail_IfInvalidPeriod()
    {
        $this->validator->checkPeriod('invalidperiod');
    }

    public function test_checkPeriod_ShouldNotFail_IfValidPeriod()
    {
        $this->assertNull($this->validator->checkPeriod('day'));
    }

    public function test_isValidPeriod()
    {
        $this->assertFalse($this->validator->isValidPeriod(null));
        $this->assertFalse($this->validator->isValidPeriod(''));
        $this->assertFalse($this->validator->isValidPeriod('invalid'));

        $this->assertTrue($this->validator->isValidPeriod('day'));
        $this->assertTrue($this->validator->isValidPeriod('week'));
        $this->assertTrue($this->validator->isValidPeriod('month'));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage General_PleaseSpecifyValue
     */
    public function test_checkName_ShouldFail_IfNameIsEmpty()
    {
        $this->validator->checkName('');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage CustomAlerts_ParmeterIsTooLong
     */
    public function test_checkName_ShouldFail_IfNameIsTooLong()
    {
        $name = range(0, 101);
        $this->validator->checkName(implode('', $name));
    }

    public function test_checkName_ShouldNotFail_IfNameIsNotEmpty()
    {
        $this->validator->checkName('b');
    }

    /**
     * @dataProvider invalidApiMethodAndMetricValidator
     * @expectedException \Exception
     */
    public function test_checkApiMethodAndMetric_ShouldFail_IfInvalid($idSite, $apiMethod, $metric, $expectedMessage)
    {
        try {
            $this->validator->checkApiMethodAndMetric($idSite, $apiMethod, $metric);
        } catch (\Exception $e) {
            $this->assertContains($expectedMessage, $e->getMessage());

            throw $e;
        }
    }

    public function invalidApiMethodAndMetricValidator()
    {
        return array(
            array(1, '', 'nb_visits', 'CustomAlerts_InvalidReport'),
            array(1, 'actionwithoutmethod', 'nb_visits', 'CustomAlerts_InvalidReport'),
            array(1, 'MultiSites_NotExisting', 'nb_visits', 'CustomAlerts_InvalidReport'),
            array(1, 'NotExisting_get', 'nb_visits', 'CustomAlerts_InvalidReport'),
            array(1, 'MultiSites_getAll', 'nb_notexist', 'CustomAlerts_InvalidMetric')
        );
    }

    public function test_checkApiMethodAndMetric_ShouldNotFail_IfValid()
    {
        $this->assertNull($this->validator->checkApiMethodAndMetric(1, 'MultiSites_getAll', 'nb_visits'));
    }

    /**
     * @dataProvider invalidMetricConditionProvider
     * @expectedException \Exception
     * @expectedExceptionMessage CustomAlerts_InvalidMetricCondition
     */
    public function test_checkMetricCondition_ShouldFail_IfInvalid($condition)
    {
        $this->validator->checkMetricCondition($condition);
    }

    public function invalidMetricConditionProvider()
    {
        return array(
            array(''),
            array(null),
            array('notExisting'),
            array(9),
        );
    }

    public function test_isValidGroupCondition()
    {
        $this->assertFalse($this->validator->isValidGroupCondition(null));
        $this->assertFalse($this->validator->isValidGroupCondition(''));
        $this->assertFalse($this->validator->isValidGroupCondition('matchesany'));

        $this->assertTrue($this->validator->isValidGroupCondition('matches_any'));
        $this->assertTrue($this->validator->isValidGroupCondition('matches_exactly'));
    }

    public function test_isValidMetricCondition()
    {
        $this->assertFalse($this->validator->isValidMetricCondition(null));
        $this->assertFalse($this->validator->isValidMetricCondition(''));
        $this->assertFalse($this->validator->isValidMetricCondition('lessthan'));

        $this->assertTrue($this->validator->isValidMetricCondition('less_than'));
        $this->assertTrue($this->validator->isValidMetricCondition('greater_than'));
    }

    public function test_isValidComparableDate()
    {
        $this->assertFalse($this->validator->isValidComparableDate('invalid', 1));
        $this->assertFalse($this->validator->isValidComparableDate('', 12));
        $this->assertFalse($this->validator->isValidComparableDate('day', 88));

        $this->assertTrue($this->validator->isValidComparableDate('day', 1));
        $this->assertTrue($this->validator->isValidComparableDate('month', 12));
    }


    public function test_checkMetricCondition_ShouldNotFail_IfValid()
    {
        $this->assertNull($this->validator->checkMetricCondition('less_than'));
    }

    /**
     * @dataProvider invalidReportConditionProvider
     * @expectedException \Exception
     * @expectedExceptionMessage CustomAlerts_InvalidReportCondition
     */
    public function test_checkReportCondition_ShouldFail_IfInvalid($condition)
    {
        $this->validator->checkReportCondition($condition);
    }

    public function invalidReportConditionProvider()
    {
        return array(
            array('notExisting'),
            array(9),
        );
    }

    public function test_checkReportCondition_ShouldNotFail_IfValid()
    {
        $this->assertNull($this->validator->checkReportCondition('matches_exactly'));
    }

    /**
     * @dataProvider invalidComparedToProvider
     * @expectedException \Exception
     * @expectedExceptionMessage CustomAlerts_InvalidComparableDate
     */
    public function test_checkComparedTo_ShouldFail_IfInvalid($period, $comparedTo)
    {
        $this->validator->checkComparedTo($period, $comparedTo);
    }

    public function invalidComparedToProvider()
    {
        return array(
            array('invalid', 1),
            array('', 12),
            array('day', 77)
        );
    }

    public function test_checkComparedTo_ShouldNotFail_IfValid()
    {
        $this->assertNull($this->validator->checkComparedTo('day', 7));
        $this->assertNull($this->validator->checkComparedTo('week', 1));
        $this->assertNull($this->validator->checkComparedTo('month', 12));
    }
}
