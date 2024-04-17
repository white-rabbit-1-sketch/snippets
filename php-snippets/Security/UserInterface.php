<?php

namespace Shared\AuthBundle\Service\Security;

/**
 * Interface UserInterface
 * @package Shared\AuthBundle\Service\Security
 */
interface UserInterface
{
    /**
     * @return string
     */
    public function getRole(): string;

    /**
     * @return int|null
     */
    public function getId(): ?int;
}