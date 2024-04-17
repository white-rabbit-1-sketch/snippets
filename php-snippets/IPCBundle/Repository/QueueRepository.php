<?php

namespace Shared\IPCBundle\Repository;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class QueueRepository
 * @package Shared\IPCBundle\Repository
 */
class QueueRepository
{
    public const DEFAULT_MESSAGE_TYPE = 1;

    protected const DEFAULT_QUEUE_PERMISSION = 0666;
    protected const DEFAULT_MESSAGE_MAX_SIZE = 16184;

    /** @var resource */
    protected $queue;

    /** @var int */
    protected $queueKey;

    /** @var int */
    protected $queuePermission;

    /**
     * QueueRepository constructor.
     * @param int $queueKey
     * @param int $queuePermission
     */
    public function __construct(
        int $queueKey,
        int $queuePermission = self::DEFAULT_QUEUE_PERMISSION
    ) {
        $this->queueKey = $queueKey;
        $this->queuePermission = $queuePermission;
    }

    public function initQueue(): void
    {
        if (!$this->queue) {
            $this->queue = \msg_get_queue($this->queueKey, $this->queuePermission);
        }
    }

    /**
     * @param mixed $message
     * @param int $messageType
     */
    public function push(
        $message,
        int $messageType = self::DEFAULT_MESSAGE_TYPE
    ): void {
        $this->initQueue();

        $result = \msg_send(
            $this->queue,
            $messageType,
            $message ,
            true ,
            false,
            $errorCode
        );

        if ($result === false) {
            throw new \RuntimeException('Cant push message to queue');
        }
    }

    /**
     * @param bool $nowait
     * @return mixed
     */
    public function pop($nowait = false)
    {
        $this->initQueue();

        $message = null;

        $result = \msg_receive(
            $this->queue,
            0,
            $messageType,
            self::DEFAULT_MESSAGE_MAX_SIZE,
            $message,
            true,
            $nowait ? MSG_IPC_NOWAIT : 0,
            $errorCode
        );

        if ($result === false) {
            throw new \RuntimeException('Cant receive message from queue');
        }

        return $message;
    }
}