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
// @todo comment all the things
// @todo coding standards

trait BehatTestTrait  {

  use BehatContainerTrait;

  protected $behatEnvironment;

  protected $stepResults;

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


  public static function assertScenarioPassed($scenarioResults, $scenario = NULL, $stepResults = [], $message = '')
  {
      $constraint = new \Drupal\Tests\ahs_miscellaneous\Unit\HasScenarioPassedConstraint($scenario, $stepResults, get_called_class());
      self::assertThat($scenarioResults, $constraint, $message);
  }

    /**
     * Store the last step.
     *
     * @param AfterStepTested $event
     */
    public function storeLastStep(AfterStepTested $event)
    {
      $this->stepResults[] = [$event->getTestResult(), $event->getStep()];
    }

    protected function getBehatDefinitionFinder() {
      return $this->getBehatContainer()->get('definition.finder');
    }
    
    protected function getBehatScenarioTester() {
      return $this->getBehatContainer()->get('tester.scenario');
    }
    
    protected function getBehatOutlineTester() {
      return $this->getBehatContainer()->get('tester.outline');
    }
    
    protected function getBehatEventDispatcher() {
      return $this->getBehatContainer()->get(\Behat\Behat\EventDispatcher\ServiceContainer\EventDispatcherExtension::DISPATCHER_ID);
    }
    
    
      protected function getBehatEnvironment() {
        if (is_null($this->behatEnvironment)) {
            $environment = new PhpUnitEnvironment(new \Behat\Testwork\Suite\GenericSuite('test',[]));
            $environment->registerContextClass(get_class($this));
            $environment = $environment;
            $environment->setTestCase($this);
            $this->behatEnvironment = $environment;
        }
        return $this->behatEnvironment;
      }
    

}