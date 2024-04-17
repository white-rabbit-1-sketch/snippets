<?php

namespace Shared\CommandBundle\Service\Handler;

use Shared\CommandBundle\Command\AbstractCommand;

/**
 * Interface HandlerInterface
 * @package Shared\CommandBundle\Service\Handler
 */
interface HandlerInterface
{
    public function handle(AbstractCommand $command): void;
}