<?php

namespace PHPUnitBehat\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnitBehat\TestTraits\BehatTestTrait;
use Behat\Gherkin\Node\KeywordNodeInterface;
use Behat\Gherkin\Node\ScenarioInterface;

/**
 * 
 */
class BehatProvidingTraitTestParent extends TestCase {

  use BehatTestTrait;

}

