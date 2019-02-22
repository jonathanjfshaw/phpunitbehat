<?php

namespace PHPUnitBehat\TestTraits;

use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Behat\EventDispatcher\Event\StepTested;
use Behat\Behat\EventDispatcher\ServiceContainer\EventDispatcherExtension;

trait BehatStepResultCollectionTrait  {

  /**
   * Recent behat steps and their results.
   *
   * @var array $behatStepResults
   */
  protected $behatStepResults = [];

  /**
   * Collect each step after it is tested, with the test result.
   *
   * @param AfterStepTested $event
   */
  public function collectBehatStepResult(AfterStepTested $event)
  {
    $this->behatStepResults[] = ['testResult' => $event->getTestResult(), 'step' => $event->getStep()];
  }

  /**
   * Get the Behat step results.
   *
   * @return array
   *   An array of behat step results, each an array with keys 'testResult' and 'step'.
   */
  public function getBehatStepResults() {
    return $this->behatStepResults;
  }

  /**
   * Start collecting Behat step results.
   */
  public function startBehatStepResultCollection() {
    $dispatcher = $this->getBehatContainer()->get(EventDispatcherExtension::DISPATCHER_ID);
    $dispatcher->addListener(StepTested::AFTER, array($this, 'collectBehatStepResult'), -999);
    // Empty the store of past step results.
    $this->behatStepResults = [];
  }    

}