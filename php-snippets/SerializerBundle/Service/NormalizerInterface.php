<?php

namespace Shared\SerializerBundle\Service;

/**
 * Class NormalizerInterface
 * @package Shared\SerializerBundle\Service
 */
interface NormalizerInterface
{
    /**
     * @param $object
     * @return mixed
     */
    public function normalize($object);

    /**
     * @return string
     */
    public static function getSubscribedClass(): string;
}
