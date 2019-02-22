<?php

namespace PHPUnitBehat\Tests;

use PHPUnitBehat\TestTraits\BehatScenarioTestingTrait;
use PHPUnitBehat\TestTraits\BehatProvidingTrait;
use Behat\Testwork\Tester\Result\TestResult;
use Behat\Behat\Tester\Result\StepResult;

/**
 * 
 */
trait TestHelpersTrait {

  use BehatScenarioTestingTrait;
  use BehatProvidingTrait;

  protected function getTestBehatFeature() {
    return $this->parseBehatFeature($this->testBehatFeature);
  }

  protected function getTestBehatScenario($index) {
    return $this->getTestBehatFeature()->getScenarios()[$index];
  }

  protected function executeTestBehatScenario($scenario) {
    return $this->executeBehatScenario($scenario, $this->getTestBehatFeature());
  }

  protected function assertTestBehatScenario($scenario) {
    return $this->assertBehatScenario($scenario, $this->getTestBehatFeature());
  }

  protected function assertTestPassed($result) {
    $this->assertTestResult(TestResult::PASSED, $result);
  }

  protected function assertTestFailed($result) {
    $this->assertTestResult(TestResult::FAILED, $result);
  }

  protected function assertTestUndefined($result) {
    $this->assertTestResult(StepResult::UNDEFINED, $result);
  }  

  protected function assertTestResult($expected, $result) {
    $this->assertInstanceOf('Behat\Testwork\Tester\Result\TestResult', $result);
    $actual = $result->getResultCode();
    $this->assertEquals($expected, $actual);
  }

  protected function expectStepExceptionMessage($result, $step) {
    $message = "$result: $step";
    $this->expectExceptionMessage($message);
  }

  protected function assertBehatScenarioAssertion($scenario, $exceptionClass = NULL, array $exceptionMessages = []) {
    if (!is_null($exceptionClass)) {
      $this->expectException($exceptionClass);
      foreach ($exceptionMessages as $message) {
        $this->expectExceptionMessage($message);
      }
    }
    $this->assertTestBehatScenario($scenario);
  }

}

