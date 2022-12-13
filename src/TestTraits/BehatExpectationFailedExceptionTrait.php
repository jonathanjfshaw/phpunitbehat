<?php

namespace PHPUnitBehat\TestTraits;

use PHPUnit\Framework\TestFailure;
use PHPUnit\Util\Filter;
use SebastianBergmann\Comparator\ComparisonFailure;
use Exception;

trait BehatExpectationFailedExceptionTrait  {

  protected $comparisonFailure;

  /**
   * @param string                 $message
   * @param ComparisonFailure|null $comparisonFailure
   * @param \Exception|null        $previous
   */

  public function __construct(string $message, ComparisonFailure $comparisonFailure = null, \Exception $previous = null)
  {
    $this->comparisonFailure = $comparisonFailure;

    parent::__construct($message, 0, $previous);
  }

  /**
   * @return null|ComparisonFailure
   */
  public function getComparisonFailure()
  {
    return $this->comparisonFailure;
  }

  /**
   * @return string
   */
  public function __toString()
  {
    $string = TestFailure::exceptionToString($this);

    if ($trace = Filter::getFilteredStacktrace($this)) {
      $string .= "\n" . $trace;
    }

    return $string;
  }
}
