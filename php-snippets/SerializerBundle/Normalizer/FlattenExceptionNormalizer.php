<?php

namespace Shared\SerializerBundle\Normalizer;

use Symfony\Component\Debug\Exception\FlattenException;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\SerializerBundle\Service\NormalizerInterface;

/**
 * Class FlattenExceptionNormalizer
 * @package Shared\SerializerBundle\Normalizer
 *
 * @DI\Service
 * @DI\Tag("shared.serializer.subscribing_handler")
 */
class FlattenExceptionNormalizer implements NormalizerInterface
{
    /**
     * @return string
     */
    public static function getSubscribedClass(): string
    {
        return FlattenException::class;
    }

    /**
     * @param FlattenException $flattenException
     * @return array
     */
    public function normalize($flattenException)
    {
        return $flattenException->toArray();
    }
}