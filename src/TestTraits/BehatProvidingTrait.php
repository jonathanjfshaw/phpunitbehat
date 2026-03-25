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
   * The current scenario being tested, set by the test method.
   */
  protected ?ScenarioInterface $currentBehatScenario = null;

  /**
   * The current feature being tested, set by the test method.
   */
  protected ?KeywordNodeInterface $currentBehatFeature = null;

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
   * Passing scenario title as the first arg is not strictly necessary 
   * but improves the readability of phpunit's CLI output of the test results.
   * 
   * @param string $feature
   *   A Behat feature.
   * 
   * @return array
   *   An array of scenarios, each an array of title, scenario, and feature.
   */
  public static function provideBehatFeature(FeatureNode $feature) {
    $scenarios = [];
    foreach ($feature->getScenarios() as $scenario) {
        if ($scenario instanceof OutlineNode)  {
          foreach ($scenario->getExamples() as $index => $example) {
            $scenarios[$scenario->getTitle() . ' #' . $index] = [$example, $feature];
          }
        }
        else {
          $scenarios[$scenario->getTitle()] = [$scenario, $feature];
        }
    }
    return $scenarios;
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
   * Set the current provided data for access from step methods.
   *
   * Call this from within a test method to make the scenario and feature
   * available via getProvidedScenario() and getProvidedFeature().
   *
   * @param \Behat\Gherkin\Node\ScenarioInterface $scenario
   * @param \Behat\Gherkin\Node\KeywordNodeInterface $feature
   */
  protected function setProvidedData(ScenarioInterface $scenario, KeywordNodeInterface $feature): void {
    $this->currentBehatScenario = $scenario;
    $this->currentBehatFeature = $feature;
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
    if ($this->currentBehatFeature !== null) {
      return $this->currentBehatFeature;
    }
    throw new \Exception("Feature not found. Call setProvidedData() in your test method before accessing the feature.");
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
    if ($this->currentBehatScenario !== null) {
      return $this->currentBehatScenario;
    }
    throw new \Exception("Scenario not found. Call setProvidedData() in your test method before accessing the scenario.");
  }

}
