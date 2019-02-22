<?php

namespace PHPUnitBehat\Tests;

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

  protected function anError() {
    throw new TestException("A test error message");
  }

}

