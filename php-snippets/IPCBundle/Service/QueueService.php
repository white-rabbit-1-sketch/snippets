<?php

namespace Shared\IPCBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\IPCBundle\Repository\QueueRepository;

/**
 * Class QueueService
 * @package Shared\IPCBundle\Service
 */
class QueueService
{
    /** @var QueueRepository */
    protected $queueRepository;

    /**
     * QueueService constructor.
     * @param QueueRepository $queueRepository
     */
    public function __construct(
        QueueRepository $queueRepository
    ) {
        $this->queueRepository = $queueRepository;
    }

    /**
     * @param mixed $message
     * @param int $messageType
     */
    public function push(
        $message,
        int $messageType = QueueRepository::DEFAULT_MESSAGE_TYPE
    ) {
        $this->queueRepository->push($message, $messageType);
    }

    /**
     * @param bool $nowait
     * @return mixed
     */
    public function pop($nowait = false)
    {
        return $this->queueRepository->pop($nowait);
    }
}
