<?php

namespace Shared\CommandBundle\Service\Builder;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\CommandBundle\Command\AbstractCommand;
use Shared\CommandBundle\Command\SendEventNotificationCommand;

/**
 * Class SendEventNotification
 * @package Shared\CommandBundle\Service\Builder
 *
 * @DI\Service("shared.command.service.builder.send-event-notification", public=true)
 */
class SendEventNotification extends AbstractBuilder
{
    protected const DATA_KEY_ATTENDEES = 'attendees';
    protected const DATA_KEY_ATTENDEE_AGENT_ID = 'agent_id';
    protected const DATA_KEY_ATTENDEE_REL = 'rel';
    protected const DATA_KEY_ATTENDEE_REL_INVITED = 'invited';

    public function buildCommand(string $name, array $data = []): AbstractCommand
    {
        $command = new SendEventNotificationCommand($data);
        $this->buildAttendees($command, $data);
        $this->entityDriver->fill($command);

        return $command;
    }

    /**
     * @param SendEventNotificationCommand $command
     * @param array $data
     */
    protected function buildAttendees(SendEventNotificationCommand $command, array $data)
    {
        $attendees = (array) (!empty($data[self::DATA_KEY_ATTENDEES]) ? $data[self::DATA_KEY_ATTENDEES] : null);

        foreach ($attendees as $attendee) {
            if (
                is_array($attendee) &&
                array_key_exists(self::DATA_KEY_ATTENDEE_REL, $attendee) &&
                array_key_exists(self::DATA_KEY_ATTENDEE_AGENT_ID, $attendee) &&
                !empty($attendee[self::DATA_KEY_ATTENDEE_AGENT_ID]) &&
                $attendee[self::DATA_KEY_ATTENDEE_REL] == self::DATA_KEY_ATTENDEE_REL_INVITED
            ) {
                $command->addInvitedAgentId((int) $attendee[self::DATA_KEY_ATTENDEE_AGENT_ID]);
            }
        }
    }
}
