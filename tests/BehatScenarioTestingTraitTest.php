<?php

namespace PHPUnitBehat\Tests;

use Behat\Mink\Exception\ExpectationException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\ExpectationFailedException;
use Behat\Testwork\Argument\Exception\UnknownParameterValueException;

/**
 *
 */
class BehatScenarioTestingTraitTest extends TestCase {

  use TestDefinitionsTrait;
  use TestHelpersTrait;

  protected $testBehatFeature = <<<'FEATURE'
Feature: BehatScenarioTestingTrait
    In order to test a feature
    We need to able to test scenarios

    Scenario: #0 Success
        Given a success

    Scenario: #1 Failure
        Given a failure

    Scenario: #2 Error
        Given an error

    Scenario: #3 Undefined
        Given a step that has no matches

    Scenario: #4 Undefined after failure
        Given a failure
        And a step that has no matches

    Scenario: #5 Bad construction
        Given a badly constructed step

    Scenario: #6 Failure after success
        Given a success
        And a failure

    Scenario: #7 Error after success
        Given a success
        And an error

    Scenario: #8 Mink expectation exception
        Given a Mink expectation exception
FEATURE;


  /**
   * Test feature scenario "#0 Success"
   */
  public function testSuccess() {
    $scenario = $this->getTestBehatScenario(0);
    $result = $this->executeTestBehatScenario($scenario);
    $this->assertTestPassed($result);
    $this->assertTestBehatScenario($scenario);
  }

  /**
   * Test scenario "#1 Failure"
   */
  public function testFailure() {
    $scenario = $this->getTestBehatScenario(1);
    $result = $this->executeTestBehatScenario($scenario);
    $this->assertTestFailed($result);
    $exceptionMessages = [
      "Scenario '#1 Failure' had steps:",
      "Failed: Given a failure",
      "Failed asserting that false is true.",
    ];
    $this->assertBehatScenarioAssertion($scenario, ExpectationFailedException::class, $exceptionMessages);
  }

  /**
   * Test scenario "#2 Error"
   */
  public function testError() {
    $scenario = $this->getTestBehatScenario(2);
    $result = $this->executeTestBehatScenario($scenario);
    $this->assertTestFailed($result);
    $exceptionMessages = [
      "Scenario '#2 Error' had steps:",
      "Failed: Given an error",
      "A test error message",
    ];
    $this->assertBehatScenarioAssertion($scenario, TestException::class, $exceptionMessages);
  }

  /**
   * Test scenario "#3 Undefined"
   */
  public function testUndefined() {
    $scenario = $this->getTestBehatScenario(3);
    $result = $this->executeTestBehatScenario($scenario);
    $this->assertTestUndefined($result);
    $exceptionMessages = [
      "Scenario '#3 Undefined' had steps:",
      "Undefined: Given a step that has no matches",
      "@Given a step that has no matches",
      "public function aStepThatHasNoMatches()",
    ];
    $this->assertBehatScenarioAssertion($scenario, ExpectationFailedException::class, $exceptionMessages);
  }

  /**
   * Test scenario "#4 Undefined after fail"
   */
  public function testUndefinedAfterFail() {
    $scenario = $this->getTestBehatScenario(4);
    $result = $this->executeTestBehatScenario($scenario);
    $this->assertTestFailed($result);
    $exceptionMessages = [
      "Scenario '#4 Undefined after fail' had steps:",
      "Failed: Given a failure",
      "Undefined: And a step that has no matches",
//      "@Given a step that has no matches",
//      "public function aStepThatHasNoMatches()",
    ];
    $this->assertBehatScenarioAssertion($scenario, ExpectationFailedException::class, $exceptionMessages);
  }

  /**
   * Test scenario "#5 Bad construction"
   */
  public function testBadConstruction() {
    $scenario = $this->getTestBehatScenario(5);
    $result = $this->executeTestBehatScenario($scenario);
    $this->assertTestFailed($result);
    $exceptionMessages = [
      "Scenario '#5 Bad construction' had steps:",
      "Failed: Given a badly constructed step",
    ];
    $this->assertBehatScenarioAssertion($scenario, UnknownParameterValueException::class, $exceptionMessages);
  }

  /**
   * Test scenario "#6 Failure after success"
   */
  public function testFailureAfterSuccess() {
    $scenario = $this->getTestBehatScenario(6);
    $result = $this->executeTestBehatScenario($scenario);
    $this->assertTestFailed($result);
    $exceptionMessages = [
      "Scenario '#1 Failure after success' had steps:",
      "Success: Given a success",
      "Failed: And a failure",
      "Failed asserting that false is true.",
    ];
    $this->assertBehatScenarioAssertion($scenario, ExpectationFailedException::class, $exceptionMessages);
  }

  /**
   * Test scenario "#7 Error after success"
   */
  public function testErrorAfterSuccess() {
    $scenario = $this->getTestBehatScenario(7);
    $result = $this->executeTestBehatScenario($scenario);
    $this->assertTestFailed($result);
    $exceptionMessages = [
      "Scenario '#7 Error after success' had steps:",
      "Success: Given a success",
      "Failed: And an error",
      "A test error message",
    ];
    $this->assertBehatScenarioAssertion($scenario, TestException::class, $exceptionMessages);
  }

  /**
   * Test scenario "#8 Mink expectation exception"
   */
  public function testMinkExpectationException() {
    // Mink ExpectationException are treated as failures not errors.
    $scenario = $this->getTestBehatScenario(8);
    $result = $this->executeTestBehatScenario($scenario);
    $this->assertTestFailed($result);
    $exceptionMessages = [
      "Scenario '#8 Mink expectation exception' had steps:",
      "Failed: Given a Mink expectation exception",
      "A mink expectation was not met",
    ];
    $this->assertBehatScenarioAssertion($scenario, ExpectationException::class, $exceptionMessages);
  }

}
