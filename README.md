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

This project currently pins symfony/dependency-injection at ^3.0 because the way we are 
accessing the Behat container is not currently compatible with Symfony 4. 
PRs to fix this are very welcome ...

The code has not been tested with Behat's 'Outline' scenarios, but it should work.

## Usage

```PHP
$drupalFinder = new \DrupalFinder\DrupalFinder();
if ($drupalFinder->locateRoot(getcwd())) {
    $drupalRoot = $drupalFinder->getDrupalRoot();
    $composerRoot = $drupalFinder->getComposerRoot();
    ...
}
```

## Examples

- [Drupal Console Launcher](https://github.com/hechoendrupal/drupal-console-launcher)
- [Drush Launcher](https://github.com/drush-ops/drush-launcher)

## License

GPL-2.0+
