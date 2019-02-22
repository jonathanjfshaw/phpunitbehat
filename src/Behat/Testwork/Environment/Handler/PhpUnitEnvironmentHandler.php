<?php

namespace PHPUnitBehat\Behat\Testwork\Environment\Handler;

use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Suite\Suite;
use Behat\Testwork\Environment\Handler\EnvironmentHandler;
use PHPUnitBehat\Behat\Testwork\Environment\PHPUnitEnvironment;

/**
 * Represents environment handler for a PhpUnitEnvironment.
 * 
 * Based on \Behat\Testwork\Environment\Handler\StaticEnvironmentHandler.
 */
class PHPUnitEnvironmentHandler implements EnvironmentHandler
{
    /**
     * {@inheritdoc}
     */
    public function supportsSuite(Suite $suite)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function buildEnvironment(Suite $suite)
    {
        return new PHPUnitEnvironment($suite);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsEnvironmentAndSubject(Environment $environment, $testSubject = null)
    {
        return $environment instanceof PHPUnitEnvironment;
    }

    /**
     * {@inheritdoc}
     */
    public function isolateEnvironment(Environment $environment, $testSubject = null)
    {
        return $environment;
    }
}
