<?php

namespace PHPUnitBehat\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
  * Mark all service definitions as public in order to
  * be able to get the services directly from the container. 
  */
class PhpUnitBehatPublicContainerDefinitionCompiler implements CompilerPassInterface {

    /**
     * Process service`s definitions.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *   The service`s container.
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($container->getDefinitions() as $definition) {
            // Mark all service definitions as public in order to
            // be able to get the services directly from the container.
            $definition->setPublic(TRUE);
        }
    }

}
