<?php

namespace PHPUnitBehat\PHPUnit\Framework;

use PHPUnit\Framework\AssertionFailedError;

/**
 * Allows wrapping an exception as an expectation failure.
 *
 * @see \PHPUnit\Framework\AssertionFailedError
 */
class AssertionFailedWrappedError extends AssertionFailedError
{

    /**
     * The wrapped error.
     */
    protected \Throwable $wrapped;

    /**
     * @param \Throwable $wrapped
     */
    public function __construct(\Throwable $wrapped)
    {
        parent::__construct($wrapped->getMessage(), $wrapped->getCode(), $wrapped->getPrevious());
        $this->wrapped = $wrapped;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $string = $this->wrapped->getMessage();

        if ($trace = $this->wrapped->getTraceAsString()) {
            $string .= "\n" . $trace;
        }

        return $string;
    }

}
