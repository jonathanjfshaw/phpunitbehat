<?php

namespace PHPUnitBehat\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnitBehat\TestTraits\BehatProvidingTrait;
use Behat\Gherkin\Node\ExampleNode;
use Behat\Gherkin\Node\ScenarioNode;

/**
 * Tests the data provider keys BehatProvidingTrait gives to scenarios.
 */
class BehatProvidingTraitKeysTest extends TestCase {

  use BehatProvidingTrait;

  protected static $uniqueTitlesFeature = <<<'FEATURE'
Feature: Unique titles
    In order to run every scenario
    We need each scenario to have its own key

    Scenario: First
      Given a success

    Scenario: Second
      Given a success

    Scenario Outline: Outlined
      Given a <result>

      Examples:
        | result  |
        | success |
        | failure |
FEATURE;

  protected static $duplicateTitlesFeature = <<<'FEATURE'
Feature: Duplicate titles
    In order to know a scenario is not being run
    We need duplicate titles to be reported

    Scenario: Duplicated
      Given a success

    Scenario: Duplicated
      Given a success
FEATURE;

  protected static $untitledScenariosFeature = <<<'FEATURE'
Feature: Untitled scenarios
    In order to run scenarios that have no title
    We need them to be keyed by something else

    Scenario:
      Given a success

    Scenario:
      Given a success
FEATURE;

  protected static $duplicateOutlineTitlesFeature = <<<'FEATURE'
Feature: Duplicate outline titles
    In order to know an example is not being run
    We need duplicate outline titles to be reported

    Scenario Outline: Outlined
      Given a <result>

      Examples:
        | result  |
        | success |

    Scenario Outline: Outlined
      Given a <result>

      Examples:
        | result  |
        | failure |
FEATURE;

  /**
   * Test that scenarios with distinct titles are keyed by their title.
   */
  public function testUniqueTitlesAreKeyedByTitle() {
    $feature = static::parseBehatFeature(static::$uniqueTitlesFeature);
    $provided = static::provideBehatFeature($feature);
    $this->assertEquals(['First', 'Second', 'Outlined #0', 'Outlined #1'], array_keys($provided));
    foreach ($provided as $arguments) {
      $this->assertCount(2, $arguments);
      $this->assertSame($feature, $arguments[1]);
    }
    $this->assertInstanceOf(ScenarioNode::class, $provided['First'][0]);
    $this->assertInstanceOf(ExampleNode::class, $provided['Outlined #0'][0]);
  }

  /**
   * Test that duplicate scenario titles are reported.
   */
  public function testDuplicateTitlesThrow() {
    $feature = static::parseBehatFeature(static::$duplicateTitlesFeature);
    [$first, $second] = $feature->getScenarios();
    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage(sprintf('"Duplicated" at line %d and line %d', $first->getLine(), $second->getLine()));
    static::provideBehatFeature($feature);
  }

  /**
   * Test that untitled scenarios are keyed by their line.
   */
  public function testUntitledScenariosAreKeyedByLine() {
    $feature = static::parseBehatFeature(static::$untitledScenariosFeature);
    [$first, $second] = $feature->getScenarios();
    $provided = static::provideBehatFeature($feature);
    $expected = ['line ' . $first->getLine(), 'line ' . $second->getLine()];
    $this->assertEquals($expected, array_keys($provided));
    $this->assertSame($first, $provided[$expected[0]][0]);
    $this->assertSame($second, $provided[$expected[1]][0]);
  }

  /**
   * Test that duplicate outline titles are reported.
   */
  public function testDuplicateOutlineTitlesThrow() {
    $feature = static::parseBehatFeature(static::$duplicateOutlineTitlesFeature);
    [$first, $second] = $feature->getScenarios();
    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage(sprintf('"Outlined #0" at line %d and line %d', $first->getExamples()[0]->getLine(), $second->getExamples()[0]->getLine()));
    static::provideBehatFeature($feature);
  }

}
