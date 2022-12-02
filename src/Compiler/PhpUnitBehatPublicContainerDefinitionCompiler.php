<?php

namespace PHPUnitBehat\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Compiles the services` definitions in the  container in order
 * to mark them as public that allows to get them form the container directly.
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
            // Mark all services` definitions as public in order to
            // have possibility get them directly from the container.
            $definition->setPublic(TRUE);
        }
    }

}
