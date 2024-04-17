<?php

namespace Shared\AuthBundle\Service\Security\Authenticator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException as SecurityAuthenticationException;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\AuthBundle\Exception\AccessDeniedException;
use Shared\AuthBundle\Exception\AuthenticationException;

/**
 * Class AbstractAuthenticator
 * @package Shared\AuthBundle\Service\Security\Authenticator
 */
abstract class AbstractAuthenticator extends AbstractGuardAuthenticator
{
    const ERROR_AUTH_REQUIRED = 'authorization_required';
    const ERROR_AUTH_FAILED = 'authorization_failed';

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    /**
     * @param Request $request
     * @param SecurityAuthenticationException $previousException
     * @return void
     * @throws AccessDeniedException
     */
    public function onAuthenticationFailure(Request $request, SecurityAuthenticationException $previousException)
    {
        throw new AuthenticationException($previousException);
    }

    /**
     * @param Request $request
     * @param SecurityAuthenticationException|null $previousException
     * @return void
     * @throws AuthenticationException
     */
    public function start(Request $request, SecurityAuthenticationException $previousException = null)
    {
        throw new AccessDeniedException($previousException);
    }

    /**
     * @return bool
     */
    public function supportsRememberMe()
    {
        return false;
    }
}