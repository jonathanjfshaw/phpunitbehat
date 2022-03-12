<?php

namespace PHPUnitBehat\Tests;

use Behat\Mink\Exception\ExpectationException;

/**
 * 
 */
trait TestDefinitionsTrait {

  /**
   * @Given a success
   */
  public function aSuccessStep() {
    $this->assertTrue(true);
  }

  /**
   * @Given an error
   */
  public function anErrorStep() {
    $this->anError();
  }

  /**
   * @Given a badly constructed step
   */
  public function aBadlyConstructedStep($missing) {
  }

  /**
   * @Given a failure
   */
  public function aFailStep() {
    $this->assertTrue(false);
  }

  /**
   * @Given a Mink expectation exception
   */
  public function aMinkExpectationExceptionStep() {
    $driver = $this->getMockBuilder('Behat\Mink\Driver\DriverInterface')->getMock();
    throw new ExpectationException("A mink expectation was not met", $driver);
  }  

  protected function anError() {
    throw new TestException("A test error message");
  }

}

