<?php

/**
Небольшой хак, делающий все кастомные сервисы lazy. Не очень верно, но очень эффективно и быстро.
*/

namespace Shared\UtilBundle\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class LazyServiceCompilerPass
 * @package Shared\UtilBundle\Compiler
 */
class LazyServiceCompilerPass implements CompilerPassInterface
{
    protected const SHARED_CLASS_PART = 'Shared\\';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        /** @var Definition[] $definitionList */
        $definitionList = $container->getDefinitions();
        foreach ($definitionList as $definition) {
            if (mb_strpos($definition->getClass(), self::SHARED_CLASS_PART, 0) !== 0) {
                continue;
            }

            $definition->setLazy(true);
        }
    }
}