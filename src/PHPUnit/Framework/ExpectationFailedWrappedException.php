<?php

namespace PHPUnitBehat\PHPUnit\Framework;

use PHPUnit\Framework\TestFailure;
use PHPUnit\Util\Filter;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * Allows wrapping an exception as an expectation failure.
 *
 * @see \PHPUnit\Framework\ExceptionWrapper and \PHPUnit\Framework\Exception
 */
class ExpectationFailedWrappedException extends ExpectationFailedException
{

    /**
     * The wrapped exception.
     */
    protected $wrapped;

    /**
     * @param \Throwable        $wrapped
     */
    public function __construct(\Throwable $wrapped)
    {
        parent::__construct($wrapped->getMessage(), NULL, $wrapped->getPrevious());
        $this->wrapped = $wrapped;
    }

    /**
     * @inheritDoc
     */
    public function getComparisonFailure()
    {
        return NULL;
    }

  /**
   * @return string
   */
  public function __toString()
  {
    $string = TestFailure::exceptionToString($this->wrapped);

    if ($trace = Filter::getFilteredStacktrace($this->wrapped)) {
      $string .= "\n" . $trace;
    }

    return $string;
  }

}
