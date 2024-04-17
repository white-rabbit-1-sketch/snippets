<?php

namespace Shared\SerializerBundle\Entity;

/**
 * Class SerializedEntityInterfaceInterface
 * @package Shared\SerializerBundle\Entity
 */
interface SerializedEntityInterface
{
    /**
     * Устанавливает свойства объекта на основе ассоциативного массива
     * @param array $propertyList
     */
    public function setFromArray(array $propertyList);

    /**
     * Возвращает свойства объекта в виде ассоциативного массива
     * @param bool $nullValues
     * @return array
     */
    public function toArray(bool $nullValues = true);
}
