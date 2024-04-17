<?php

namespace Shared\CommandBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\CommandBundle\Command\AbstractCommand;
use Shared\CommandBundle\Service\Handler\HandlerInterface;
use Shared\UtilBundle\Entity\ContainerTrait;
use Shared\UtilBundle\Entity\LoggerTrait;

/**
 * Class HandlerService
 * @package Shared\CommandBundle\Service
 *
 * @DI\Service("shared.command.service.handler", public=true)
 * @DI\Tag("monolog.logger", attributes = {"channel" = "system.command"})
 */
class HandlerService
{
    use LoggerTrait;
    use ContainerTrait;

    protected const COMMAND_HANDLER_PATTERN = 'shared.command.service.handler.%s';

    /**
     * @param AbstractCommand $command
     */
    public function handle(AbstractCommand $command): void
    {
        $this->logger->info('Run command', [
            'command' => $command->getName(),
            'data' => $command->getParameterList()
        ]);

        $this->getCommandHandler($command)->handle($command);
    }

    /**
     * @param AbstractCommand $command
     * @return HandlerInterface
     */
    protected function getCommandHandler(AbstractCommand $command): HandlerInterface
    {
        /** @var HandlerInterface $handler */
        $handler = $this->container->get(sprintf(self::COMMAND_HANDLER_PATTERN, $command->getName()));

        return $handler;
    }
}
