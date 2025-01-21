<?php

namespace PHPUnitBehat\Tests;

use Behat\Gherkin\Node\KeywordNodeInterface;
use Behat\Gherkin\Node\ScenarioInterface;

/**
 * Tests that BehatProvidingTrait can discover features in child classes.
 */
class BehatProvidingTraitChildTest extends BehatProvidingTraitTestParent {

  protected static $feature = <<<'FEATURE'
Feature: BehatProvidingTrait
    In order to test a feature
    We need to able provide it to phpunit 

    Scenario: getProvidedScenario
      Then getProvidedScenario returns a scenario with the title "getProvidedScenario"

    Scenario: getProvidedFeature
      Then getProvidedScenario returns a scenario with the title "getProvidedFeature"
      Then getProvidedFeature returns a feature with the title "BehatProvidingTrait"
     
FEATURE;


  /**
   * @Then getProvidedScenario returns a scenario with the title :title
   */
  public function getProvidedScenarioGets($title) {
    $this->assertInstanceOf(ScenarioInterface::class, $this->getProvidedScenario());
    $this->assertEquals($title, $this->getProvidedScenario()->getTitle());
  }

  /**
   * @Then getProvidedFeature returns a feature with the title :title
   */
  public function getProvidedFeatureGets($title) {
    $this->assertInstanceOf(KeywordNodeInterface::class, $this->getProvidedFeature());
    $this->assertEquals($title, $this->getProvidedFeature()->getTitle());
  }

}

