<?php

namespace Shared\SerializerBundle\Service\Builder;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\SerializerBundle\Helper\BuilderTrait;
use Shared\UtilBundle\Entity\StringTrait;

/**
 * Class ObjectBuilder
 * @package Shared\SerializerBundle\Service\Builder
 *
 * @DI\Service("shared.serializer.service.builder.array", public=true)
 */
class ArrayBuilder extends AbstractBuilder
{
    use StringTrait;
    use BuilderTrait;

    protected static $excludeNullRuleFieldList = [
        'errorList' => null,
        'warningList' => null,
        'violationList' => null,
        'exception' => null,
        'endpointList' => null,
        'accessToken' => null,
    ];

    /**
     * @param $data
     * @param $level
     * @return array
     */
    protected function handleType($data, $level)
    {
        $result = [];

        foreach ($data as $key => $value) {
            if ($level > 1 && is_null($value)) {
                continue;
            }

            $value = $this->builderService->build($value, $level);
            /* This is NOT mistake! Dont delete it. */
            if (
                ($level > 1 || array_key_exists($key, self::$excludeNullRuleFieldList)) &&
                (is_null($value) || (is_array($value) && empty($value)))
            ) {
                continue;
            }

            $result[$this->stringService->underscore($key)] = $value;
        }

        return $result;
    }

    /**
     * @param $data
     * @return bool
     */
    protected function isTypeValid($data): bool
    {
        return is_array($data);
    }
}
