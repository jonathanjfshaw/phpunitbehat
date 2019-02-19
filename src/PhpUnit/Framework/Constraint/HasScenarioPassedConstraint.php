<?php

namespace PhpUnitBehat\PhpUnit\Framework\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use Behat\Behat\Output\Node\Printer\Helper\ResultToStringConverter;
use Behat\Testwork\Tester\Result\ExceptionResult;
use PHPUnit\Framework\ExpectationFailedException;

class HasScenarioPassedConstraint extends Constraint
{

  protected $scenario;
  protected $stepResults;
  protected $testCase;

  public function __construct($scenario = NULL, $stepResults = [], $testCase = NULL)
  {
      $this->scenario = $scenario;
      $this->stepResults = $stepResults;
      $this->testCase = $testCase;
  }

    /**
     * Returns a string representation of the constraint.
     */
    public function toString(): string
    {
        return 'scenario passed';
    }
    /**
     * Evaluates the constraint for the specified scenario results.
     *
     * @param mixed $results value or object to evaluate
     */
    protected function matches($scenarioResults): bool
    {
        return $scenarioResults->isPassed();
    }

    protected function failureDescription($scenarioResults): string
    {
      $desc = is_null($this->scenario) ? "'" . $this->scenario->getTitle . "' ": '';
      $desc .= $this->toString();
      return $desc;
    }

    protected function additionalFailureDescription($scenarioResults): string
    {
      $converter = new ResultToStringConverter();
      $steps = [];
      $exceptionString = '';
      foreach ($this->stepResults as $stepResult) {
          $result = $stepResult[0];
          $step = $stepResult[1];
          $resultString = ucfirst($converter->convertResultToString($result));
          $stepString = $step->getKeyword() . ' ' . $step->getText();
          $steps[] = $resultString . ': ' . $stepString;
          if ($result instanceof ExceptionResult && $exception = $result->getException()) {
            if (!$exception instanceof ExpectationFailedException) {
              // @todo assterioninvalidargsexception
            $exceptionString = "\nIn the failed step there was an exception:\n" . $this->getExceptionString($exception, get_class($this));
            }
          }
      }
      $intro = !is_null($this->scenario) ? "Scenario '" . $this->scenario->getTitle() . "':": '"Steps:"';
      $desc =  ".\n" . $intro . "\n" . implode("\n", $steps) . "\n" . $exceptionString . "\n";
      return $desc;
    }

  
    protected function getExceptionString($exception) {
      // If the testcase is unknown, return the usual exception output.
      if (is_null($this->testCase)) {
        return (string) $exception;
      }

      // Prepare the first line of the exception message.
      $topLine = "Exception: " . $exception->getMessage() . " in " . $exception->getFile() . ":" . $exception->getLine() . "\n";
  
      // Find where in the stack the testcase class is.
      $trace = $exception->getTrace();
      foreach ($trace as $layer => $call) {
        $class = isset($call['class']) ? $call['class'] : '';
        $maxDepth = $layer;
        if ($class === $this->testCase) {
          break;
        }
      }
  
      // Drop classes above the testcase class from the stack trace.
      $traceStringed = explode("\n", $exception->getTraceAsString());
      $traceSliced = array_slice($traceStringed, 0, $maxDepth + 1);
      $traceLines = implode("\n", $traceSliced); 
  
      $exceptionString = $topLine . $traceLines;
      return $exceptionString;
    }
}