<?php

namespace PHPUnitBehat\TestTraits;

use Behat\Gherkin\Loader\ArrayLoader;
use Behat\Gherkin\Lexer;
use Behat\Gherkin\Parser;
use Behat\Gherkin\Keywords\ArrayKeywords;
use Behat\Gherkin\Node\FeatureNode;

trait BehatProvidingTrait  {

  /** 
   * Parses the text of a Behat feature into an array.
   * 
   * @param string $featureString
   *   The text of a Behat feature.
   * @param \Behat\Gherkin\Keywords\ArrayKeywords|Null $keywords (optional)
   *   Keywords to be used to interpret the Behat feature.
   * 
   * @return Behat\Gherkin\Node\FeatureNode
   *   A Behat feature.
   */
  public function parseBehatFeature($featureString, $keywords = NULL) {
    $lexer  = new Lexer($this->getBehatKeywords($keywords));
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
  public function provideBehatFeature(FeatureNode $feature) {
    $scenarios = [];
    foreach ($feature->getScenarios() as $scenario) {
        if ($scenario instanceof OutlineNode)  {
          foreach ($outline->getExamples() as $example) {
            $scenarios[] = [$example->getTitle(), $example, $feature];
          }
        }
        else {
          $scenarios[] = [$scenario->getTitle(), $scenario, $feature];
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
  protected function getBehatKeywords($keywords = NULL) {
    if (is_null($keywords)) {
      $keywords = $this->getBehatDefaultKeywords();
    }
    return $keywords;
  }

  /**
   * Get the default keywords used to interpret Behat features.
   *
   * @return \Behat\Gherkin\Keywords\ArrayKeywords
   */
  public function getBehatDefaultKeywords() {
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

}