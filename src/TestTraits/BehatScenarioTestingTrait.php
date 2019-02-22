<?php

namespace PHPUnitBehat\TestTraits;

use PHPUnitBehat\PHPUnit\Framework\Constraint\HasScenarioPassedConstraint;
use Behat\Behat\Tester\ServiceContainer\TesterExtension;
use Behat\Behat\Context\ServiceContainer\ContextExtension;

trait BehatScenarioTestingTrait  {

  use BehatStepResultCollectionTrait;
  use BehatContainerTrait;
  use BehatEnvironmentTrait;

  public static function assertBehatScenarioPassed($scenarioResults, $scenario = NULL, $stepResults = [], $snippetGenerator = NULL, $environment = NULL, $message = '', $callHandler = '')
  {

    $constraint = new HasScenarioPassedConstraint($scenario, $stepResults, $callHandler, $snippetGenerator, $environment);
    self::assertThat($scenarioResults, $constraint, $message);
  }

  public function executeBehatScenario($scenario, $feature) {
    $tester = $this->getBehatContainer()->get(TesterExtension::SCENARIO_TESTER_ID);
    $scenarioResults = $tester->test($this->getBehatEnvironment(), $feature, $scenario, false);
    return $scenarioResults;
  }

  public function assertBehatScenario($scenario, $feature) {
    $this->startBehatStepResultCollection();
    $snippetGenerator = $this->getBehatContainer()->get(ContextExtension::CONTEXT_SNIPPET_GENERATOR_ID);
    $scenarioResults = $this->executeBehatScenario($scenario, $feature);
    $this->assertBehatScenarioPassed($scenarioResults, $scenario, $this->getBehatStepResults(), $snippetGenerator, $this->getBehatEnvironment());
  } 

}