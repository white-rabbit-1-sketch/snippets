<?php

namespace Shared\CommandBundle\Command;

use Doctrine\Common\Collections\Collection;

use Shared\ChatBundle\Entity\Chat;
use Shared\DbBundle\Annotation\Entity;

/**
 * Class SendEventNotificationCommand
 * @package Shared\CommandBundle\Command
 */
class SendEventNotificationCommand extends AbstractCommand
{
    public const NAME = 'send-event-notification';

    protected const PARAM_CHANNEL = 'channel';
    protected const PARAM_SITE_ID = 'site_id';
    protected const PARAM_CHAT_ID = 'chat_id';
    protected const PARAM_EVENT_ID = 'msg_ids';
    protected const PARAM_INVITED_AGENT_ID_LIST = 'invited_agent_id_list';

    /**
     * @var Chat
     *
     * @Entity(
     *     "Shared\ChatBundle\Entity\Chat",
     *     mapping={"siteId":"siteId", "chatId":"chatId"}
     * )
     */
    protected $chat;

    /**
     * @var Collection
     *
     * @Entity(
     *     "Shared\ChatBundle\Entity\ChatEvent",
     *     mapping={"siteId":"siteId", "chatId":"chatId", "eventId":"eventIdList"},
     *     list=true
     * )
     */
    protected $eventList;

    /**
     * @var Collection
     *
     * @Entity(
     *     "Shared\AgentBundle\Entity\Agent",
     *     mapping={"agentId":"invitedAgentIdList"},
     *     list=true
     * )
     */
    protected $invitedAgentList;

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->getParam(self::PARAM_CHANNEL);
    }

    /**
     * @param string $channel
     */
    public function setChannel(string $channel): void
    {
        $this->setParam(self::PARAM_CHANNEL, $channel);
    }

    /**
     * @return int
     */
    public function getSiteId(): int
    {
        return $this->getParam(self::PARAM_SITE_ID);
    }

    /**
     * @param int $siteId
     */
    public function setSiteId(int $siteId): void
    {
        $this->setParam(self::PARAM_SITE_ID, $siteId);
    }

    /**
     * @return int
     */
    public function getChatId(): int
    {
        return $this->getParam(self::PARAM_CHAT_ID);
    }

    /**
     * @param int $chatId
     */
    public function setChatId(int $chatId): void
    {
        $this->setParam(self::PARAM_CHAT_ID, $chatId);
    }

    /**
     * @return array
     */
    public function getEventIdList(): array
    {
        return $this->getParam(self::PARAM_EVENT_ID);
    }

    /**
     * @param array $eventIdList
     */
    public function setEventIdList(array $eventIdList): void
    {
        $this->setParam(self::PARAM_EVENT_ID, $eventIdList);
    }

    /**
     * @return Collection
     */
    public function getEventList(): Collection
    {
        return $this->eventList;
    }

    /**
     * @param Collection $eventList
     */
    public function setEventList(Collection $eventList): void
    {
        $this->eventList = $eventList;
    }

    /**
     * @return Chat
     */
    public function getChat(): Chat
    {
        return $this->chat;
    }

    /**
     * @param Chat $chat
     */
    public function setChat(Chat $chat): void
    {
        $this->chat = $chat;
    }

    /**
     * @return array
     */
    public function getInvitedAgentIdList(): array
    {
        return (array) $this->getParam(self::PARAM_INVITED_AGENT_ID_LIST);
    }

    /**
     * @param int $invitedAgentId
     */
    public function addInvitedAgentId(int $invitedAgentId): void
    {
        $invitedAgentIdList = $this->getInvitedAgentIdList();
        $invitedAgentIdList[] = $invitedAgentId;

        $this->setParam(self::PARAM_INVITED_AGENT_ID_LIST, $invitedAgentIdList);
    }

    /**
     * @return Collection
     */
    public function getInvitedAgentList(): Collection
    {
        return $this->invitedAgentList;
    }

    /**
     * @param Collection $invitedAgentList
     */
    public function setInvitedAgentList(Collection $invitedAgentList): void
    {
        $this->invitedAgentList = $invitedAgentList;
    }
}