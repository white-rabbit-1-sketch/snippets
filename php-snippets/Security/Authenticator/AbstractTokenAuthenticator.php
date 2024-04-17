<?php

namespace Shared\AuthBundle\Service\Security\Authenticator;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\ChainUserProvider;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\TokenBundle\Entity\AbstractToken;
use Shared\AuthBundle\Service\User\Authorize\AbstractAuthorizeService;
use Shared\TokenBundle\Service\AbstractTokenService;

/**
 * Class AbstractTokenAuthenticator
 * @package Shared\AuthBundle\Service\Security\Authenticator
 *
 * @DI\Service("shared.auth.service.security.token_authenticator", public=true)
 */
abstract class AbstractTokenAuthenticator extends AbstractAuthenticator
{
    const TOKEN_HEADER_NAME = 'Authorization';

    /** @var AbstractTokenService */
    protected $tokenService;

    /**
     * @param AbstractTokenService $tokenService
     */
    public function __construct(AbstractTokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * @param Request $request
     * @return null|AbstractToken
     */
    public function getCredentials(Request $request)
    {
        return $this->makeTokenByHash(
            $this->getCredentialsHash($request)
        );
    }

    /**
     * @param Request $request
     * @return null|string
     */
    protected function getCredentialsHash(Request $request): ?string
    {
        return $request->headers->get(self::TOKEN_HEADER_NAME) ?? $request->query->get('token');
    }

    /**
     * @param null|string $hash
     * @return null|AbstractToken
     */
    protected function makeTokenByHash(?string $hash): ?AbstractToken
    {
        /** @var AbstractToken $token */
        $token = $hash && $this->isValidHash($hash) ? $this->tokenService->makeTokenByHash($hash) : null;

        return $token;
    }

    /**
     * @param AbstractToken $token
     * @param UserProviderInterface $userProvider
     * @return null|UserInterface
     */
    public function getUser($token, UserProviderInterface $userProvider)
    {
        /** @var ChainUserProvider $userProvider */
        /** @var UserProviderInterface $provider */
        $provider = $this->determineTokenProvider($token, $userProvider);

        return $provider ? $provider->loadUserByUsername($token) : null;
    }

    /**
     * @param AbstractToken $token
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($token, UserInterface $user)
    {
        $result = false;

        if ($token instanceof AbstractToken) {
            /**
             * @TODO: What if we have old version of token ? Try to handle it anyway! Wanna remove in later.
             */
            $isTokenHaveAuthScope =
                $token->getCreateTime() < (new \DateTime('2017-02-01'))->getTimestamp() ||
                $this->tokenService->isTokenHaveScope($token, AbstractAuthorizeService::SCOPE_AUTH);

            $result = $isTokenHaveAuthScope && !$this->tokenService->isTokenExpired($token);
        }

        return $result;
    }

    /**
     * @param AbstractToken $token
     * @param UserProviderInterface $userProvider
     * @return null|UserProviderInterface
     */
    protected function determineTokenProvider(
        AbstractToken $token,
        UserProviderInterface $userProvider
    ): ?UserProviderInterface {
        $result = null;

        $providerList = [];
        if ($userProvider instanceof ChainUserProvider) {
            $providerList = $userProvider->getProviders();
        } else {
            $providerList[] = $userProvider;
        }

        /** @var UserProviderInterface $provider */
        foreach ($providerList as $provider) {
            if ($this->isProviderSupportToken($provider, $token)) {
                $result = $provider;

                break;
            }
        }

        return $result;
    }

    /**
     * @param UserProviderInterface $provider
     * @param AbstractToken $token
     * @return bool
     */
    abstract protected function isProviderSupportToken(UserProviderInterface $provider, AbstractToken $token): bool;

    /**
     * @param string $hash
     * @return bool
     */
    abstract protected function isValidHash(string $hash): bool;
}