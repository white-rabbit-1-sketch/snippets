<?php

namespace Shared\AuthBundle\Service\Security;

/**
 * Interface UserProviderInterface
 * @package Shared\AuthBundle\Service\Security
 */
interface UserProviderInterface
{
    /**
     * @param $user
     * @return string
     */
    public function getUserName($user): string;

    /**
     * @param $token
     * @return mixed
     */
    public function getUserByToken($token);

    /**
     * @return array
     */
    public function getAvailableRoleList(): array;

    /**
     * @param $user
     * @return string
     */
    public function getUserRole($user): string;

    /**
     * @param $user
     * @return array
     */
    public function getEndpointList($user): array;

    /**
     * @param $user
     * @return int|null
     */
    public function getUserId($user): ?int;
}