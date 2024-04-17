<?php

namespace Shared\SerializerBundle\Service\Builder;

/**
 * Class AbstractBuilder
 * @package Shared\SerializerBundle\Service\Builder
 */
abstract class AbstractBuilder
{
    /**
     * @param $data
     * @param int $level
     * @return mixed
     */
    public function buildType($data, int $level)
    {
        if (!$this->isTypeValid($data)) {
            throw new \InvalidArgumentException('Invalid type');
        }

        return $this->handleType($data, $level);
    }

    /**
     * @param $data
     * @param $level
     * @return mixed
     */
    abstract protected function handleType($data, $level);

    /**
     * @param $data
     * @return bool
     */
    abstract protected function isTypeValid($data): bool;
}
