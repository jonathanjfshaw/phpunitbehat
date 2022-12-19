<?php

namespace PHPUnitBehat\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnitBehat\PHPUnit\Framework\AssertionFailedWrappedError;

/**
 *
 */
class AssertionFailedWrappedErrorTest extends TestCase {

  use TestDefinitionsTrait;

  // This code using AssertionFailedWrappedError is taken from the project's readme.

  use TestHelpersTrait {
    assertBehatScenarioPassed as assertBehatScenarioPassedTrait;
  }

  public static function assertBehatScenarioPassed($scenarioResults, $scenario = NULL, $stepResults = [], $snippetGenerator = NULL, $environment = NULL, $message = '', $callHandler = '')
  {
    try {
      self::assertBehatScenarioPassedTrait($scenarioResults, $scenario, $stepResults, $snippetGenerator, $environment, $message, $callHandler);
    }
    catch (TestWrappingException $e) {
      throw new \PHPUnitBehat\PHPUnit\Framework\AssertionFailedWrappedError($e);
    }
  }

  protected $testBehatFeature = <<<'FEATURE'
Feature: BehatScenarioTestingTrait
    In order to test a feature
    We need to able to test scenarios

    Scenario: #0 Normal error
        Given an error

    Scenario: #1 Error to wrap 
        Given an error to wrap

FEATURE;


  /**
   * Test scenario "#0 Error"
   */
  public function testError() {
    $scenario = $this->getTestBehatScenario(0);
    $result = $this->executeTestBehatScenario($scenario);
    $this->assertTestFailed($result);
    $exceptionMessages = [
      "Scenario '#0 Error' had steps:",
      "Failed: Given an error",
      "A test error message",
    ];
    $this->assertBehatScenarioAssertion($scenario, TestException::class, $exceptionMessages);
  }

  /**
   * Test scenario "#1 Error"
   */
  public function testErrorToWrap() {
    $scenario = $this->getTestBehatScenario(1);
    $result = $this->executeTestBehatScenario($scenario);
    $this->assertTestFailed($result);
    $exceptionMessages = [
      "Scenario '#1 Error to wrap' had steps:",
      "Failed: Given an error to wrap",
      "A wrapped test error message",
    ];
    $this->assertBehatScenarioAssertion($scenario, AssertionFailedWrappedError::class, $exceptionMessages);
  }

  /**
   * @Given an error to wrap
   */
  public function anErrorToWrapStep() {
    throw new TestWrappingException("A wrapped test error message");
  }

}
