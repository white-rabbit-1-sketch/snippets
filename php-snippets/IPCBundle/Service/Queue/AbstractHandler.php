<?php

namespace Shared\IPCBundle\Service\Queue;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\IPCBundle\Service\QueueService;

/**
 * Class AbstractHandler
 * @package Shared\IPCBundle\Service
 */
abstract class AbstractHandler
{
    /** @var QueueService */
    protected $queueService;

    /**
     * AbstractHandler constructor.
     * @param QueueService $queueService
     */
    public function __construct(
        QueueService $queueService
    ) {
        $this->queueService = $queueService;
    }

    /**
     * @param int|null $messagesLimit
     */
    public function handle(?int $messagesLimit = null): void
    {
        $messagesCount = 0;

        while (true) {
            $this->execute($this->queueService->pop());
            $messagesCount++;

            if (is_int($messagesLimit) && $messagesCount >= $messagesLimit) {
                break;
            }
        }
    }

    /**
     * @param mixed $message
     */
    abstract protected function execute($message): void;
}
