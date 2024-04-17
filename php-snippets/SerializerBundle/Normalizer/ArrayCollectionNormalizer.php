<?php

namespace Shared\SerializerBundle\Normalizer;

use Doctrine\Common\Collections\ArrayCollection;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\SerializerBundle\Service\NormalizerInterface;

/**
 * Class ArrayCollectionNormalizer
 * @package Shared\SerializerBundle\Normalizer
 *
 * @DI\Service
 * @DI\Tag("shared.serializer.subscribing_handler")
 */
class ArrayCollectionNormalizer implements NormalizerInterface
{
    /**
     * @return string
     */
    public static function getSubscribedClass(): string
    {
        return ArrayCollection::class;
    }

    /**
     * @param ArrayCollection $arrayCollection
     * @return array
     */
    public function normalize($arrayCollection)
    {
        return $arrayCollection->toArray();
    }
}