<?php

namespace PHPUnitBehat\TestTraits;

trait BehatTestTrait  {

  use BehatScenarioTestingTrait;
  use BehatProvidingTrait;
  
  /**
   * Data provider for ::testBehatScenario().
   *
   * Parses the ::feature property as a Behat feature,
   * breaking it down into individual scenarios for testing.
   */
  public function providerTestBehatScenario() {
    $feature = $this->parseBehatFeature($this->feature);
    return $this->provideBehatFeature($feature);
  }

  /**
   * Test a Behat scenario.
   * 
   * @dataProvider providerTestBehatScenario
   */
  public function testBehatScenario($scenario, $feature) {
    $this->assertBehatScenario($scenario, $feature);
  }

}