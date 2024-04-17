<?php

namespace Shared\SerializerBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\UtilBundle\Entity\ContainerTrait;

/**
 * Class NormalizerService
 * @package Shared\SerializerBundle\Service
 *
 * @DI\Service("shared.serializer.service.normalizer", public=true)
 */
class NormalizerService
{
    use ContainerTrait;

    protected const NORMALIZER_PATTERN = 'shared.serializer.service.normalizer.%s';

    protected const PROXY_CLASS_PREFIX = 'Proxies\\__CG__\\';

    /**
     * @param string $class
     * @return string
     */
    public function getServiceIdByClass(string $class): string
    {
        return sprintf(self::NORMALIZER_PATTERN, mb_strtolower(str_replace('\\', '_', $class)));
    }

    /**
     * @param $object
     * @return string
     */
    public function getServiceIdByObject($object): string
    {
        $class = str_replace(self::PROXY_CLASS_PREFIX, '', get_class($object));

        return $this->getServiceIdByClass($class);
    }

    /**
     * @param $object
     * @return bool
     */
    public function isObjectHasNormalizer($object): bool
    {
        return $this->container->has($this->getServiceIdByObject($object));
    }

    /**
     * @param $object
     * @return mixed
     */
    public function normalize($object)
    {
        /** @var NormalizerInterface $normalizer */
        $normalizer = $this->container->get($this->getServiceIdByObject($object));

        return $normalizer->normalize($object);
    }
}
