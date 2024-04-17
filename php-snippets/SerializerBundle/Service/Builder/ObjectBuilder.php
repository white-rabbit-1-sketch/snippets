<?php

namespace Shared\SerializerBundle\Service\Builder;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\SerializerBundle\Entity\SerializedEntityInterface;
use Shared\SerializerBundle\Helper\BuilderTrait;
use Shared\SerializerBundle\Helper\NormalizerTrait;
use Shared\UtilBundle\Entity\StringTrait;

/**
 * Class ObjectBuilder
 * @package Shared\SerializerBundle\Service\Builder
 *
 * @DI\Service("shared.serializer.service.builder.object", public=true)
 */
class ObjectBuilder extends AbstractBuilder
{
    use StringTrait;
    use BuilderTrait;
    use NormalizerTrait;

    /**
     * @param $object
     * @param $level
     * @return mixed
     */
    protected function handleType($object, $level)
    {
        if ($this->normalizerService->isObjectHasNormalizer($object)) {
            $result = $this->builderService->build($this->normalizerService->normalize($object), --$level);
        } elseif($object instanceof \stdClass) {
            $result = $object;
        } else {
            $result = $this->builderService->build($this->processObject($object, $level), --$level);
        }

        return $result;
    }

    /**
     * @param SerializedEntityInterface $object
     * @param int $level
     * @return array
     */
    protected function processObject(SerializedEntityInterface $object, int $level): array
    {
        return $object->toArray($level == 1);
    }

    /**
     * @param $data
     * @return bool
     */
    protected function isTypeValid($data): bool
    {
        return is_object($data);
    }
}
