# PHPUnitBehat

PHPUnitBehat provides traits that allow developers to create tests that combine 
the power & flexibility of  PHPUnit with the readability of Behat.

By including these traits in your PHPUnit test classes you can:
- use PHPUnit as your test runner as usual
- parse (Behat) Gherkin features in your PHPUnit tests
- write features as text in your PHPUnit test classes
- execute features in your PHPUnit tests
- declare methods in your PHPUnit test that are automatically matched to feature steps
- have the scenario results output as PHPUnit test results


## Requirements

* Behat 3
* PHPUnit 6+

## Installation

`composer require jonathanjfshaw/phpunitbehat`

## Usage

A selection of test traits are provided that help you to make use of Behat
within PHPUnit with varying degress of control.

The simplest way is to use `PHPUnitBehat\TestTraits\BehatTestTrait` 
in your test's base class.

```PHP
namespace MyProject\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnitBehat\TestTraits\BehatTestTrait;

class MyTestBase extends TestCase {
  use BehatTestTrait;
}
```
Create a test that extends the base class, and define a 
$feature property with corresponding step methods.

```PHP
namespace MyProject\Tests;

class MyTest extends MyTestBase {

  protected $feature = <<<'FEATURE'
Feature: Demo feature
  In order to demonstrate testing a feature in phpUnit
  We define a simple feature in the class

  Scenario: Success
    Given a step that succeeds    

  Scenario: Failure
    When a step fails
    
  Scenario: Undefined
    Then there is a step that is undefined
FEATURE;

  /**
   * @Given a step that succeeds
   */
  public function aStepThatSucceeds() {
    $this->assertTrue(true);
  }

  /**
   * @When a step fails
   */
  public function aStepFails() {
    $this->assertTrue(false);
  }

}
```

Execute your test as usual with phpunit. You should see test output like this:

```
Testing
Test 'MyProject\Tests\myTest::testBehatScenario with data set #0 ('Success', Behat\Gherkin\Node\ScenarioNode Object (...), Behat\Gherkin\Node\FeatureNode Object (...))' started
Test 'MyProject\Tests\myTest::testBehatScenario with data set #0 ('Success', Behat\Gherkin\Node\ScenarioNode Object (...), Behat\Gherkin\Node\FeatureNode Object (...))' ended
Test 'MyProject\Tests\myTest::testBehatScenario with data set #1 ('Failure', Behat\Gherkin\Node\ScenarioNode Object (...), Behat\Gherkin\Node\FeatureNode Object (...))' started
Test 'MyProject\Tests\myTest::testBehatScenario with data set #1 ('Failure', Behat\Gherkin\Node\ScenarioNode Object (...), Behat\Gherkin\Node\FeatureNode Object (...))' ended
Test 'MyProject\Tests\myTest::testBehatScenario with data set #2 ('Undefined', Behat\Gherkin\Node\ScenarioNode Object (...), Behat\Gherkin\Node\FeatureNode Object (...))' started
Test 'MyProject\Tests\myTest::testBehatScenario with data set #2 ('Undefined', Behat\Gherkin\Node\ScenarioNode Object (...), Behat\Gherkin\Node\FeatureNode Object (...))' ended


Time: 26.6 seconds, Memory: 54.25MB

There were 2 failures:

1) MyProject\Tests\myTest::testBehatScenario with data set #1 ('Failure', Behat\Gherkin\Node\ScenarioNode Object (...), Behat\Gherkin\Node\FeatureNode Object (...))
Scenario 'Failure' had steps:
Failed: When a step fails

Failed asserting that false is true.

/tests/myTest.php:35

2) MyProject\Tests\myTest::testBehatScenario with data set #2 ('Undefined', Behat\Gherkin\Node\ScenarioNode Object (...), Behat\Gherkin\Node\FeatureNode Object (...))
Failed asserting that  scenario passed.
Scenario 'Undefined' had steps:
Undefined: Then there is a step that is undefined


You can define these undefined steps in your PHPUnit test class like this:

  /**
   * @Then there is a step that is undefined
   */
  public function thereIsAStepThatIsUndefined() {

  }

```


## Running tests

You can specify individual scenarios to run because the scenario title is given as the data provider name. For example to test a scenario with title `Undefined`:
```
phpunit --filter '@Undefined'
```
More complex possibilities exist; see https://phpunit.de/manual/6.5/en/textui.html#textui.examples.filter-patterns.

## Failures and errors

Phpunit will report any instance of AssertionFailedError as a failure and other exceptions as errors. In some circumstances you may wish to display certain exceptions as errors. For example, if you're using Mink for web assertions, you may want to report Mink's ExpectationExpection (which is thrown when web content does not match an expectation) as a failure not an error.

To achieve this, in your test's base class you can catch these errors when scenario results are examined, and rethrow them using the provided AssertionFailedWrappedError:

```
  use BehatTestTrait {
    assertBehatScenarioPassed as assertBehatScenarioPassedTrait;
  }

  public static function assertBehatScenarioPassed($scenarioResults, $scenario = NULL, $stepResults = [], $snippetGenerator = NULL, $environment = NULL, $message = '', $callHandler = '')
  {
    try {
      self::assertBehatScenarioPassedTrait($scenarioResults, $scenario, $stepResults, $snippetGenerator, $environment, $message, $callHandler);
    }
    catch (\Behat\Mink\Exception\ExpectationException $e) {
      throw new \PHPUnitBehat\PHPUnit\Framework\AssertionFailedWrappedError($e);
    }
  }
```

## License

MIT.
