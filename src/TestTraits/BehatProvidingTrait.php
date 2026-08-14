<?php

namespace PHPUnitBehat\TestTraits;

use Behat\Gherkin\Loader\ArrayLoader;
use Behat\Gherkin\Lexer;
use Behat\Gherkin\Parser;
use Behat\Gherkin\Keywords\ArrayKeywords;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Gherkin\Node\KeywordNodeInterface;
use Behat\Gherkin\Node\ScenarioInterface;

trait BehatProvidingTrait  {

  /** 
   * Parses the text of a Behat feature into an array.
   * 
   * @param string $featureString
   *   The text of a Behat feature.
   * @param \Behat\Gherkin\Keywords\ArrayKeywords|Null $keywords (optional)
   *   Keywords to be used to interpret the Behat feature.
   * 
   * @return \Behat\Gherkin\Node\FeatureNode
   *   A Behat feature.
   */
  public static function parseBehatFeature($featureString, $keywords = NULL) {
    $lexer  = new Lexer(static::getBehatKeywords($keywords));
    $parser = new Parser($lexer);
    $feature = $parser->parse($featureString);
    return $feature;
  }


  /** 
   * Breaks a Behat feature object into an array of scenarios 
   * suitable to be supplied by a phpunit data provider for 
   * individual testing.
   * 
   * Scenarios are keyed by their title, which phpunit uses to name the test.
   * Keys must be unique: were two scenarios to share one, phpunit would run
   * only the last of them, without reporting that the others were dropped.
   *
   * @param string $feature
   *   A Behat feature.
   *
   * @return array
   *   An array of scenarios, each an array of scenario and feature, keyed by
   *   scenario title.
   *
   * @throws \RuntimeException
   *   If two scenarios would be given the same key.
   */
  public static function provideBehatFeature(FeatureNode $feature) {
    $scenarios = [];
    // The line each key came from, so a collision can name both scenarios.
    $lines = [];
    foreach (static::getKeyedBehatScenarios($feature) as [$key, $scenario]) {
      if (isset($lines[$key])) {
        throw new \RuntimeException(sprintf('Feature "%s" has more than one scenario keyed "%s" at line %d and line %d. Scenario keys must be unique, or phpunit would run only one of these scenarios.', $feature->getTitle(), $key, $lines[$key], $scenario->getLine()));
      }
      $lines[$key] = $scenario->getLine();
      $scenarios[$key] = [$scenario, $feature];
    }
    return $scenarios;
  }

  /**
   * List a feature's scenarios and examples with the key each would be given.
   *
   * @param \Behat\Gherkin\Node\FeatureNode $feature
   *   A Behat feature.
   *
   * @return array
   *   An array of pairs of key and scenario, in the order they are declared.
   */
  protected static function getKeyedBehatScenarios(FeatureNode $feature) {
    $keyed = [];
    foreach ($feature->getScenarios() as $scenario) {
      if ($scenario instanceof OutlineNode)  {
        foreach ($scenario->getExamples() as $index => $example) {
          $keyed[] = [static::getBehatScenarioKey($scenario) . ' #' . $index, $example];
        }
      }
      else {
        $keyed[] = [static::getBehatScenarioKey($scenario), $scenario];
      }
    }
    return $keyed;
  }

  /**
   * Get the key by which a scenario or outline is provided.
   *
   * @param \Behat\Gherkin\Node\ScenarioInterface $scenario
   *   A Behat scenario or outline.
   *
   * @return string
   *   The scenario title, or its line if it has no title.
   */
  protected static function getBehatScenarioKey(ScenarioInterface $scenario) {
    $title = $scenario->getTitle();
    // Untitled scenarios have nothing but their position to distinguish them.
    if (is_null($title) || trim($title) === '') {
      return 'line ' . $scenario->getLine();
    }
    return $title;
  }

  /**
   * Get the keywords to use to interpret Behat features.
   *
   * @param \Behat\Gherkin\Keywords\ArrayKeywords|null $keywords
   *   The keywords to use.
   * 
   * @return \Behat\Gherkin\Keywords\ArrayKeywords
   */
  protected static function getBehatKeywords($keywords = NULL) {
    if (is_null($keywords)) {
      $keywords = static::getBehatDefaultKeywords();
    }
    return $keywords;
  }

  /**
   * Get the default keywords used to interpret Behat features.
   *
   * @return \Behat\Gherkin\Keywords\ArrayKeywords
   */
  public static function getBehatDefaultKeywords() {
    return new ArrayKeywords([
        'en' => [
            'feature'          => 'Feature',
            'background'       => 'Background',
            'scenario'         => 'Scenario',
            'scenario_outline' => 'Scenario Outline|Scenario Template',
            'examples'         => 'Examples|Scenarios',
            'given'            => 'Given',
            'when'             => 'When',
            'then'             => 'Then',
            'and'              => 'And',
            'but'              => 'But'
        ],
      ]);
    }

  /**
   * Get the current feature.
   *
   * This is intended to be called from within a test method or test setUp
   * method, where it is sometimes useful to have access to the feature for
   * prettier troubleshooting output.
   *
   * @return \Behat\Gherkin\Node\KeywordNodeInterface
   */
  protected function getProvidedFeature() {
    $data = NULL;
    if (method_exists($this, 'getProvidedData')) {
      $data = $this->getProvidedData();
    }
    elseif (method_exists($this, 'providedData')) {
      $data = $this->providedData();
    }
    if (is_array($data) && $feature = $data[1]) {
      if ($feature instanceof KeywordNodeInterface) {
        return $feature;
      }
    }
    throw new \Exception("Feature not found in provided data.");
  }

  /**
   * Get the current scenario or example.
   *
   * This is intended to be called from within a test method or test setUp
   * method, where it is sometimes useful to have access to the scenario for
   * prettier troubleshooting output.
   *
   * @return \Behat\Gherkin\Node\ScenarioInterface
   *   The current scenario or example.
   */
  protected function getProvidedScenario() {
    $data = NULL;
    if (method_exists($this, 'getProvidedData')) {
      $data = $this->getProvidedData();
    }
    elseif (method_exists($this, 'providedData')) {
      $data = $this->providedData();
    }
    if (is_array($data) && $scenario = $data[0]) {
      if ($scenario instanceof ScenarioInterface) {
        return $scenario;
      }
    }
    throw new \Exception("Scenario not found in provided data.");
  }

}
