<?php

namespace PHPUnitBehat\PHPUnit\Framework\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use Behat\Behat\Output\Node\Printer\Helper\ResultToStringConverter;
use Behat\Testwork\Tester\Result\ExceptionResult;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\Exception as PHPUnitException;
use \Exception;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Behat\Tester\Result\ExecutedStepResult;
use Behat\Behat\Tester\Result\StepResult;


class HasScenarioPassedConstraint extends Constraint
{

  /**
   * The behat environment.
   *
   * @var \PHPUnitBehat\Behat\Testwork\Environment\PHPUnitEnvironment
   */
  protected $environment;

  /**
   * The behat scenario.
   *
   * @var \Behat\Gherkin\Node\ScenarioNode
   */
  protected $scenario;

  /**
   * An array of step results with keys 'step' and 'testResult'.
   *
   * @var array
   */
  protected $stepResults;

  /**
   * The name of the class executing the scenario step calls.
   *
   * @var string
   */
  protected $scenarioCallHandler;

  /**
   * The snippet generator.
   *
   * @var [type]
   */
  protected $snippetGenerator;

  /**
   * Undocumented variable
   *
   * @var [type]
   */
  protected $snippetTemplate = <<<TPL
  /**
   * @%%s %s
   */
  public function %s(%s) {

  }
TPL;

  /**
   * Construct a new constraint.
   *
   * @param \Behat\Gherkin\Node\ScenarioNode|NULL $scenario
   *   A Behat scenario.
   * @param array $stepResults
   *   An array of Behat step results, with keys 'step' and 'testResult'. 
   * @param string $scenarioCallHandler
   *   The name of the class the executed the scenario step calls.
   */
  public function __construct($scenario = NULL, $stepResults = [], $scenarioCallHandler = '', $snippetGenerator = NULL, $environment = NULL)
  {
      $this->scenario = $scenario;
      $this->stepResults = $stepResults;
      $this->scenarioCallHandler = empty($scenarioCallHandler) ? 'Behat\Testwork\Call\Handler\RuntimeCallHandler' : $scenarioCallHandler;
      $this->snippetGenerator = $snippetGenerator;
      $this->environment = $environment;
  }

    /**
     * {@inheritdoc}
     */
    public function toString(): string
    {
        return 'scenario passed';
    }

    /**
     * {@inheritdoc}
     */
    protected function failureDescription($other): string
    {
        // Because we throw exceptions in ::bubbleStepResults(),
        // this is only used for undefined steps, not failing steps.       
        return ' ' . $this->toString();
    }

    /**
     * {@inheritdoc}
     */
    protected function additionalFailureDescription($other): string
    {   
        // Because we throw exceptions in ::bubbleStepResults(),
        // we expect to only use this for undefined steps, not failing steps.
        $stepsMessage = $this->stepResultsMessage($this->stepResults);
        $snippetsMessage = $this->snippetsMessage();
        return "$stepsMessage\n\n$snippetsMessage";
    }

    /**
     * {@inheritdoc}
     */
    protected function matches($scenarioResults): bool
    {
        $this->bubbleStepResults();
        return $scenarioResults->isPassed();
    }
  
    /**
     * Force exceptions and stdout from steps to bubble up into phpunit.
     * 
     * Behat's RuntimeCallHandler catches these during step execution 
     * and stores them on the call result.
     */
    protected function bubbleStepResults() {
      $stepsSoFar = [];
      foreach($this->stepResults as $stepResult) {
        $stepsSoFar[] = $stepResult;
        $result = $stepResult['testResult'];
        if ($result instanceof ExecutedStepResult && $result->getCallResult()->hasStdOut()) {
          print_r($result->getCallResult()->getStdOut());
        }
        if ($result instanceof ExceptionResult && $exception = $result->getException()) {
          // Modify the exception to truncate the trace
          $this->truncateExceptionTrace($exception, $this->scenarioCallHandler);
          $this->modifyExceptionMessage($exception, $stepsSoFar);
          throw $exception;
        }
      }
    }

    /**
     * Adds details about the scenario steps so far to a step exception message.
     *
     * @param \Exception $exception
     *   An exception throw in a step.
     * @param array $stepsSoFar
     *   An array of steps executed up to this point in this scenario.
     */
    protected function modifyExceptionMessage(Exception &$exception, array $stepsSoFar) {
      $traceReflector = new \ReflectionProperty('Exception', 'message');
      $traceReflector->setAccessible(true);
      $originalMessage = $traceReflector->getValue($exception);
      $stepResultsMessage = $this->stepResultsMessage($stepsSoFar);
      $modifiedMessage = "\n$stepResultsMessage\n\n$originalMessage";
      $traceReflector->setValue($exception, $modifiedMessage);
    }

    /**
     * A list of steps executed in a scenario.
     *
     * @param array $stepResults
     *   An array of steps, each an array with keys 'step' and 'testResult'.
     * @return string
     *   A string list of steps.
     */
    protected function stepResultsMessage(array $stepResults) {
      $converter = new ResultToStringConverter();
      $intro = !is_null($this->scenario) ? "Scenario '" . $this->scenario->getTitle() . "' had steps:": '"Steps:"';
      $steps = [];
      foreach ($this->stepResults as $stepResult) {
          $result = $stepResult['testResult'];
          $step = $stepResult['step'];
          $resultString = ucfirst($converter->convertResultToString($result));
          $stepString = $step->getKeyword() . ' ' . $step->getText();
          $steps[] = $resultString . ': ' . $stepString;
      }
      $message =  "$intro\n" . implode("\n", $steps);
      return $message;
    }

    /**
     * Truncate an exception trace at a certain ceiling.
     *
     * @param \Exception $exception
     *   The exception whose trace should be truncated.
     * @param string $ceiling
     *   The name of the class at whose first occurrence to truncate.
     */
    protected function truncateExceptionTrace(Exception &$exception, $ceiling) {
      if ($exception instanceof PHPUnitException) {
        // PhpUnit's trace is copied to a serializableTrace property
        // when the exception s created and this is used when rendering to string. 
        $reflectionClassName = 'PHPUnit\FrameworkException\Exception';
        $traceReflector = new \ReflectionProperty('PHPUnit\Framework\Exception', 'serializableTrace');
        $fullTrace = $exception->getSerializableTrace();
      }
      else {
        $traceReflector = new \ReflectionProperty('Exception', 'trace');
        $fullTrace = $exception->getTrace();
      }
      $traceReflector->setAccessible(true);
      $trace = $this->truncateTraceArray($fullTrace, $ceiling);
      $traceReflector->setValue($exception, $trace);
    }

    /**
     * Truncate a trace array at a certain ceiling
     *
     * @param array $trace
     *   An exception stack trace array.
     * @param string $ceiling
     *   The name of the class at whose first occurrence to truncate.
     *
     * @return array
     *   A truncated trace array.
     */
    protected function truncateTraceArray($trace, $ceiling)
    {
        if (empty($ceiling)) {
          return $trace;
        }
        foreach ($trace as $layer => $call) {
          $class = isset($call['class']) ? $call['class'] : '';
          $position = $layer;
          if ($class === $ceiling) {
            break;
          }
        }
        return array_slice($trace, 0, $position - 1);
    }

    protected function snippetsMessage() {
      $this->setupSnippetGenerator();
      $snippets = [];
      foreach ($this->stepResults as $stepResult) {
        $result = $stepResult['testResult'];
        $step = $stepResult['step'];
        if ($result->getResultCode() == StepResult::UNDEFINED) {
          $snippets[] = $this->generateSnippet($step);
        }
      }
      $message = "\nYou can define these undefined steps in your PHPUnit test class like this:\n\n" . implode("\n\n", $snippets);
      return $message;
    }

    protected function setupSnippetGenerator() {
      // Set the context on the snippet generator.
      $context = $this->environment->getContextClasses()[0];
      $identifier = new \Behat\Behat\Context\Snippet\Generator\FixedContextIdentifier($context);
      $this->snippetGenerator->setContextIdentifier($identifier);

      // Modify the snippet generator's template so that it no longer
      // refers to PendingException().
      $templateReflector = new \ReflectionProperty(get_class($this->snippetGenerator), 'templateTemplate');
      $templateReflector->setAccessible(true);
      $templateReflector->setValue($this->snippetGenerator, $this->snippetTemplate);
  }

    protected function generateSnippet($step) {
      if (!is_null($this->snippetGenerator) && !is_null($this->environment)) {
        return $this->snippetGenerator->generateSnippet($this->environment, $step)->getSnippet();
      }
    }
    

}
