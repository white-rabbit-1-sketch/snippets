<?php

namespace Shared\CommandBundle\Service\Builder;

use Shared\CommandBundle\Command\AbstractCommand;

/**
 * Interface BuilderInterface
 * @package Shared\CommandBundle\Service\Builder
 */
interface BuilderInterface
{
    /**
     * @param string $name
     * @param array $data
     * @return AbstractCommand
     */
    public function buildCommand(string $name, array $data = []): AbstractCommand;
}