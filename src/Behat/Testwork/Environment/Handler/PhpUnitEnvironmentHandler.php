<?php

namespace PhpUnitBehat\Behat\Testwork\Environment\Handler;

use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Suite\Suite;
use Behat\Testwork\Environment\Handler\EnvironmentHandler;
use PhpUnitBehat\Behat\Testwork\Environment\PhpUnitEnvironment;

/**
 * Represents environment handler for a PhpUnitEnvironment.
 * 
 * Based on \Behat\Testwork\Environment\Handler\StaticEnvironmentHandler
 */
class PhpUnitEnvironmentHandler implements EnvironmentHandler
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
        return new PhpUnitEnvironment($suite);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsEnvironmentAndSubject(Environment $environment, $testSubject = null)
    {
        return $environment instanceof PhpUnitEnvironment;
    }

    /**
     * {@inheritdoc}
     */
    public function isolateEnvironment(Environment $environment, $testSubject = null)
    {
        return $environment;
    }
}
