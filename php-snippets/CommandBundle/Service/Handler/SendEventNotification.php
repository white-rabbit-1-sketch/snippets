<?php

namespace Shared\CommandBundle\Service\Handler;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\ChannelBundle\Service\Channel\Chat\Group\Event\PublisherService;
use Shared\CommandBundle\Command\AbstractCommand;
use Shared\CommandBundle\Command\SendEventNotificationCommand;

/**
 * Class SendEventNotification
 * @package Shared\CommandBundle\Service\Handler
 *
 * @DI\Service("shared.command.service.handler.send-event-notification", public=true)
 */
class SendEventNotification implements HandlerInterface
{
    /** @var PublisherService */
    protected $publisherService;

    /**
     * @param PublisherService $publisherService
     *
     * @DI\InjectParams({
     *     "publisherService" = @DI\Inject("shared.channel.service.channel.chat.group.event.publisher")
     * })
     */
    public function __construct(PublisherService $publisherService)
    {
        $this->publisherService = $publisherService;
    }

    /**
     * @param AbstractCommand $command
     */
    public function handle(AbstractCommand $command): void
    {
        /** @var SendEventNotificationCommand $command */
        $this->publisherService->publishEventList(
            $command->getChannel(),
            $command->getChat(),
            $command->getEventList()->toArray(),
            $command->getInvitedAgentList()->toArray()
        );
    }
}
