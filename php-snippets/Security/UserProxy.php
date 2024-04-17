<?php

namespace Shared\AuthBundle\Service\Security;

use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;

use Shared\AgentBundle\Entity\Agent;
use Shared\PartnerBundle\Entity\Partner;
use Shared\SuperUserBundle\Entity\SuperUser;
use Shared\ClientBundle\Entity\Client;

/**
 * Class UserProxy
 * @package Shared\AuthBundle\Service\Security
 */
class UserProxy implements SecurityUserInterface, UserInterface
{
    /** @var UserProviderInterface */
    protected $userProvider;

    /** @var Agent|Partner|SuperUser|Client */
    protected $user;

    /**
     * UserProxy constructor.
     * @param UserProviderInterface $userProvider
     * @param $user
     */
    public function __construct(UserProviderInterface $userProvider, $user)
    {
        $this->userProvider = $userProvider;
        $this->user = $user;
    }

    /**
     * @param $methodName
     * @param array $parameterList
     * @return mixed
     */
    public function __call($methodName, array $parameterList)
    {
        return call_user_func_array([$this->user, $methodName], $parameterList);
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return [$this->getRole()];
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->userProvider->getUserRole($this->user);
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->userProvider->getUserId($this->user);
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->userProvider->getUserName($this->user);
    }

    public function getPassword()
    {

    }

    public function getSalt()
    {

    }

    public function eraseCredentials()
    {

    }
}