<?php

namespace Shared\SerializerBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Shared\SerializerBundle\Compiler\SerializerTagCompilerPass;

/**
 * Class SharedSerializerBundle
 * @package Shared\SerializerBundle
 */
class SharedSerializerBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SerializerTagCompilerPass());

        parent::build($container);
    }
}
