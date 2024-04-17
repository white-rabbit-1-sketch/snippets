<?php

namespace Shared\SerializerBundle\Compiler;

use Shared\SerializerBundle\Service\NormalizerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\SerializerBundle\Helper\NormalizerTrait;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class SerializerTagCompilerPass
 * @package Shared\SerializerBundle\Compiler
 *
 * @DI\Service
 */
class SerializerTagCompilerPass implements CompilerPassInterface
{
    use NormalizerTrait;

    protected const SERIALIZER_NORMALIZER_TAG = 'shared.serializer.subscribing_handler';
    protected const SERIALIZER_NORMALIZER_SERVICE_ID = 'shared.serializer.service.normalizer';

     /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->normalizerService = $container->get(self::SERIALIZER_NORMALIZER_SERVICE_ID);

        $taggedServiceIdList = $container->findTaggedServiceIds(self::SERIALIZER_NORMALIZER_TAG);

        foreach ($taggedServiceIdList as $taggedServiceId => $data) {
            /** @var Definition $normalizerDefinition */
            $normalizerDefinition = $container->getDefinition($taggedServiceId);
            $normalizerClass = $normalizerDefinition->getClass();

            if (!is_subclass_of($normalizerClass, NormalizerInterface::class)) {
                throw new \InvalidArgumentException(sprintf(
                    'Normalizer with id "%s" must implement "%s"',
                    $taggedServiceId,
                    NormalizerInterface::class
                ));
            }

            $normalizerServiceId = $this->normalizerService->getServiceIdByClass($normalizerClass::getSubscribedClass());

            $container->setDefinition($normalizerServiceId, $normalizerDefinition);
        }
    }
}