<?php

namespace Behat\Behat\Context\Environment\ContextEnvironment;

use Behat\Testwork\Suite\Suite;
use PHPUnit_Framework_TestCase;
use Behat\Testwork\Call\Callee;
use Behat\Behat\Context\Exception\ContextNotFoundException;

class PhpUnitEnvironment implements ContextEnvironment
{
    // \Behat\Behat\Context\Environment\UninitializedContextEnvironment
    // implements \Behat\Behat\Context\Environment\UninitializedContextEnvironment
    //extends \Behat\Testwork\Environment\StaticEnvironment
    /**
     * @var array[]
     */
    private $contextClasses = array();

    /**
     * Registers context class.
     *
     * @param string     $contextClass
     * @param null|array $arguments
     *
     * @throws ContextNotFoundException   If class does not exist
     */
    public function registerContextClass($contextClass, array $arguments = null)
    {
        if (!class_exists($contextClass)) {
            throw new ContextNotFoundException(sprintf(
                '`%s` context class not found and can not be used.',
                $contextClass
            ), $contextClass);
        }
        $this->contextClasses[$contextClass] = $arguments ? : array();
    }

    /**
     * {@inheritdoc}
     */
    public function hasContexts()
    {
        return count($this->contextClasses) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getContextClasses()
    {
        return array_keys($this->contextClasses);
    }

    /**
     * {@inheritdoc}
     */
    public function hasContextClass($class)
    {
        return isset($this->contextClasses[$class]);
    }

    /**
     * Returns context classes with their arguments.
     *
     * @return array[]
     */
    public function getContextClassesWithArguments()
    {
        return $this->contextClasses;
    }

    /**
     * Initializes environment.
     *
     * @param Suite $suite
     */
    public function __construct(Suite $suite)
    {
        $this->suite = $suite;
    }

    /**
     * Specifies the current PhpUnit test case.
     *
     * @param PHPUnit_Framework_TestCase $testCase
     */
    public function setTestCase(PHPUnit_Framework_TestCase $testCase)
    {
        $this->testCase = $testCase;
    }

    /**
     * {@inheritdoc}
     */
    public function getSuite()
    {
        return $this->suite;
    }

    /**
     * {@inheritdoc}
     */
    public function bindCallee(Callee $callee)
    {
        $method = $callee->getCallable()[1];
        return [$this->testCase, $method];
    }

}