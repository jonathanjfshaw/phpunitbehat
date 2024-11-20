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
  public static function providerTestBehatScenario() {
    $feature = self::parseBehatFeature(self::$feature);
    return self::provideBehatFeature($feature);
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