<?php

namespace PhpUnitBehat\PhpUnit\Framework;

use Behat\Gherkin\Loader\ArrayLoader;
use Behat\Gherkin\Lexer;
use Behat\Gherkin\Parser;
use Behat\Gherkin\Keywords\ArrayKeywords;

use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Behat\EventDispatcher\Event\StepTested;
use Behat\Gherkin\Node\OutlineNode;

// @todo use as package
// @todo stack exits on first call to class, should be last call to class
// @todo Is phpUnitCallHandler really necessary? what's with the output buffering? Can we flush it?
// @todo does the environment really need to implement contextenvironment
// @todo handle assertioninvalidargsexception in constraint
// @todo proper aPI for getting stepResults
// @todo comment all the things
// @todo coding standards

trait BehatTestTrait  {

  use BehatHelpersTrait;

  protected function getFeatureDefaultKeywordsEn() {

    return new ArrayKeywords([
        'en' => [
            'feature'          => 'Feature',
            'background'       => 'Background',
            'scenario'         => 'Scenario',
            'scenario_outline' => 'Scenario Outline|Scenario Template',
            'examples'         => 'Examples|Scenarios',
            'given'            => 'Given',
            'when'             => 'When',
            'then'             => 'Then',
            'and'              => 'And',
            'but'              => 'But'
        ],
      ]);
    }
  
    /**
   * Data provider for ::testFeature().
   */
  public function providerTestFeature() {
    $lexer  = new Lexer($this->getFeatureDefaultKeywordsEn());
    $parser = new Parser($lexer);
    $feature = $parser->parse($this->feature);
    $scenarios = [];
    foreach ($feature->getScenarios() as $scenario) {
        $scenarios[] = [$feature, $scenario];
    }
    return $scenarios;
  }

    /**
   * @dataProvider providerTestFeature
   */
  public function testFeature($feature, $scenario) {
    // Setup tracking of the scenario step results.
    $this->getBehatEventDispatcher()->addListener(StepTested::AFTER, array($this, 'storeLastStep'), -999);
    $this->stepResults = [];

    // Test the scenario and report detailed results if failed.
    $tester = $scenario instanceof OutlineNode ? $this->getBehatOutlineTester() : $this->getBehatScenarioTester();
    $scenarioResults = $tester->test($this->getBehatEnvironment(), $feature, $scenario, false);
    $this->assertScenarioPassed($scenarioResults, $scenario, $this->stepResults);
  }

}