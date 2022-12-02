<?php

namespace PHPUnitBehat\TestTraits;

use Behat\Testwork\ServiceContainer\ExtensionManager;
use Behat\Testwork\ServiceContainer\ContainerLoader;
use PHPUnitBehat\Compiler\PhpUnitBehatPublicContainerDefinitionCompiler;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\Definition;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Behat\Definition\ServiceContainer\DefinitionExtension;
use Behat\Behat\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Behat\Gherkin\ServiceContainer\GherkinExtension;
use Behat\Behat\Hook\ServiceContainer\HookExtension;
use Behat\Behat\Output\ServiceContainer\Formatter\JUnitFormatterFactory;
use Behat\Behat\Output\ServiceContainer\Formatter\PrettyFormatterFactory;
use Behat\Behat\Output\ServiceContainer\Formatter\ProgressFormatterFactory;
use Behat\Behat\HelperContainer\ServiceContainer\HelperContainerExtension;
use Behat\Behat\Snippet\ServiceContainer\SnippetExtension;
use Behat\Behat\Tester\ServiceContainer\TesterExtension;
use Behat\Behat\Transformation\ServiceContainer\TransformationExtension;
use Behat\Behat\Translator\ServiceContainer\GherkinTranslationsExtension;
use Behat\Testwork\Argument\ServiceContainer\ArgumentExtension;
use Behat\Testwork\Autoloader\ServiceContainer\AutoloaderExtension;
use Behat\Testwork\Call\ServiceContainer\CallExtension;
use Behat\Testwork\Cli\ServiceContainer\CliExtension;
use Behat\Testwork\Environment\ServiceContainer\EnvironmentExtension;
use Behat\Testwork\Exception\ServiceContainer\ExceptionExtension;
use Behat\Testwork\Filesystem\ServiceContainer\FilesystemExtension;
use Behat\Testwork\Ordering\ServiceContainer\OrderingExtension;
use Behat\Testwork\Output\ServiceContainer\Formatter\FormatterFactory;
use Behat\Testwork\Output\ServiceContainer\OutputExtension;
use Behat\Testwork\ServiceContainer\ServiceProcessor;
use Behat\Testwork\Specification\ServiceContainer\SpecificationExtension;
use Behat\Testwork\Suite\ServiceContainer\SuiteExtension;
use Behat\Testwork\Translator\ServiceContainer\TranslatorExtension;



trait BehatContainerTrait  {

    protected static $behatContainer;

    // see https://dzone.com/articles/practical-php-testing/practical-php-testing-patterns-23
    // If this trait is used in a base test class that many test classes extend, then
    // the container will only be built once during a phpUnit execution, not once
    // per test class, because ::behatContainer is a static property.   
    protected function getBehatContainer() {
      if (self::$behatContainer === null) {
        $this->setBehatContainer();
      }  
      return self::$behatContainer;
    } 

  protected function setBehatContainer() {
    // Create the container.
    $extensionManager = new ExtensionManager($this->getBehatExtensions());
    $containerLoader = new ContainerLoader($extensionManager);
    $containerBuilder = new ContainerBuilder;

    // Provide basic parameters required by Behat, even though they make no sense in PhpUnit.
    $containerBuilder->setParameter('paths.base', '');
    $containerBuilder->set('cli.input', new ArrayInput([]));
    // Set default command`s name that require Behat.
    // The command`s name can`t be empty.
    $containerBuilder->setParameter('cli.command.name', 'PHPUnitBehat');
    $containerBuilder->set('cli.output', new NullOutput());

    // Add the PhpUnit behat environment handler.
    $definition = new Definition('PHPUnitBehat\Behat\Testwork\Environment\Handler\PHPUnitEnvironmentHandler');
    $definition->addTag('environment.handler', array('priority' => 0));
    $containerBuilder->setDefinition('environment.handler.phpunit', $definition);

    // Finalise the container.
    $containerLoader->load($containerBuilder, []);
    $containerBuilder->addObjectResource($containerLoader);
    // Added compiler pass in order to process service`s definitions.
    $containerBuilder->addCompilerPass(new PhpUnitBehatPublicContainerDefinitionCompiler(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1);
    $containerBuilder->compile();
    self::$behatContainer = $containerBuilder;
  }


  /**
   * Get an array of Behat extensions.
   * 
   * These have all been verified as needed for our purposes, except
   * GherkinTranslationExtension, HookExtension and TransformationExtension 
   * which simply look like they might be useful.
   * 
   * They are all heavily interdependent.
   *
   * @return array
   */
  protected function getBehatExtensions()
  {
      // The commented out lines are Behat default extensions which we don't need.
      $processor = new \Behat\Testwork\ServiceContainer\ServiceProcessor();
      return array(
        new ArgumentExtension(),
        new AutoloaderExtension(array('' => '%paths.base%/features/bootstrap')),
        new SuiteExtension($processor),
//        new ExceptionExtension($processor),
        new GherkinExtension($processor),
        new CallExtension($processor),
        new TranslatorExtension(),
        new GherkinTranslationsExtension(),
        new TesterExtension($processor),
//        new CliExtension($processor),
        new EnvironmentExtension($processor),
        new SpecificationExtension($processor),
        new FilesystemExtension(),
        new ContextExtension($processor),
//        new SnippetExtension($processor),
        new DefinitionExtension($processor),
        new EventDispatcherExtension($processor),
        new HookExtension(),
        new TransformationExtension($processor),
 //       new OrderingExtension($processor),
//        new HelperContainerExtension($processor)
    );
  }

}
