<?php

namespace Shared\SerializerBundle\Normalizer;

use Doctrine\ORM\PersistentCollection;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\SerializerBundle\Service\NormalizerInterface;

/**
 * Class PersistentCollectionNormalizer
 * @package Shared\SerializerBundle\Normalizer
 *
 * @DI\Service
 * @DI\Tag("shared.serializer.subscribing_handler")
 */
class PersistentCollectionNormalizer implements NormalizerInterface
{
    /**
     * @return string
     */
    public static function getSubscribedClass(): string
    {
        return PersistentCollection::class;
    }

    /**
     * @param PersistentCollection $persistentCollection
     * @return array
     */
    public function normalize($persistentCollection)
    {
        return $persistentCollection->toArray();
    }
}