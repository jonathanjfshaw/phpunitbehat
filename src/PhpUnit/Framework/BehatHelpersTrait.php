<?php

namespace PhpUnitBehat\PhpUnit\Framework;

use Behat\Gherkin\Loader\ArrayLoader;
use Behat\Gherkin\Lexer;
use Behat\Gherkin\Parser;
use Behat\Gherkin\Keywords\ArrayKeywords;

use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Behat\EventDispatcher\Event\StepTested;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Testwork\Suite\GenericSuite;
use Behat\Behat\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use PhpUnitBehat\Behat\Testwork\Environment\PhpUnitEnvironment;
use PhpUnitBehat\PhpUnit\Framework\Constraint\HasScenarioPassedConstraint;

// @todo stack exits on first call to class, should be last call to class
// @todo Is phpUnitCallHandler really necessary? what's with the output buffering? Can we flush it?
// @todo use as package
// @todo does the environment really need to implement contextenvironment
// @todo handle assertioninvalidargsexception in constraint
// @todo refer to services by interface constants in get helpers
// @todo refactor test trait to add more helpers
// @todo comment all the things
// @todo coding standards

trait BehatHelpersTrait  {

  use BehatContainerTrait;

  protected $behatEnvironment;

  public $stepResults;

  public function getFeatureDefaultKeywordsEn() {

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
  


  public static function assertScenarioPassed($scenarioResults, $scenario = NULL, $stepResults = [], $message = '')
  {
      $constraint = new HasScenarioPassedConstraint($scenario, $stepResults, get_called_class());
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

    public function getBehatDefinitionFinder() {
      return $this->getBehatContainer()->get('definition.finder');
    }
    
    public function getBehatScenarioTester() {
      return $this->getBehatContainer()->get('tester.scenario');
    }
    
    public function getBehatOutlineTester() {
      return $this->getBehatContainer()->get('tester.outline');
    }
    
    public function getBehatEventDispatcher() {
      return $this->getBehatContainer()->get(EventDispatcherExtension::DISPATCHER_ID);
    }
    
    
      public function getBehatEnvironment() {
        if (is_null($this->behatEnvironment)) {
            $environment = new PhpUnitEnvironment(new GenericSuite('test',[]));
            $environment->registerContextClass(get_class($this));
            $environment = $environment;
            $environment->setTestCase($this);
            $this->behatEnvironment = $environment;
        }
        return $this->behatEnvironment;
      }
    

}