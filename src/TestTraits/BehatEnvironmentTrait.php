<?php

namespace PHPUnitBehat\TestTraits;

use Behat\Testwork\Suite\GenericSuite;
use PHPUnitBehat\Behat\Testwork\Environment\PHPUnitEnvironment;

trait BehatEnvironmentTrait  {

  /**
   * @var \PHPUnitBehat\Behat\Testwork\Environment\PHPUnitEnvironment $behatEnvironment
   *   A Behat environment that works with phpunit.
   */
  protected $behatEnvironment;

  /**
   * Get a Behat environment suitable for PHP unit.
   *
   * @return \PHPUnitBehat\Behat\Testwork\Environment\PHPUnitEnvironment
   *   A Behat environment that works with phpunit.
   */
  public function getBehatEnvironment() {
    if (is_null($this->behatEnvironment)) {
        $environment = new PHPUnitEnvironment(new GenericSuite('test',[]));
        $environment->registerContextClass(get_class($this));
        $environment = $environment;
        $environment->setTestCase($this);
        $this->behatEnvironment = $environment;
    }
    return $this->behatEnvironment;
  }

}