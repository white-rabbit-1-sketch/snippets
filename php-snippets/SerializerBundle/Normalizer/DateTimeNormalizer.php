<?php

namespace Shared\SerializerBundle\Normalizer;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\SerializerBundle\Service\NormalizerInterface;

/**
 * Class DateTimeNormalizer
 * @package Shared\SerializerBundle\Normalizer
 *
 * @DI\Service
 * @DI\Tag("shared.serializer.subscribing_handler")
 */
class DateTimeNormalizer implements NormalizerInterface
{
    /**
     * @return string
     */
    public static function getSubscribedClass(): string
    {
        return \DateTime::class;
    }

    /**
     * @param \DateTime $dateTime
     * @return int
     */
    public function normalize($dateTime)
    {
        return $dateTime->getTimestamp();
    }
}