<?php

/**
Утилита
*/

namespace Shared\UtilBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class StdService
 * @package Shared\UtilBundle\Service
 *
 * @DI\Service("shared.util.service.std", public=true)
 */
class StdService
{
    /**
     * @param \stdClass $stdClass
     * @return array
     */
    public function castStdToArray(\stdClass $stdClass): array
    {
        $propertyList = (array) $stdClass;

        foreach ($propertyList as $propertyName => &$propertyValue) {
            if (is_object($propertyValue)) {
                $propertyValue = $this->castStdToArray($propertyValue);
            }
        }

        return $propertyList;
    }
}
