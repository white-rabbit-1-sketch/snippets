<?php

namespace Shared\AuthBundle\Service\Security\Authenticator\Client;

use Symfony\Component\Security\Core\User\UserProviderInterface;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\AuthBundle\Service\Security\Authenticator\AbstractTokenAuthenticator;
use Shared\TokenBundle\Entity\AbstractToken;
use Shared\TokenBundle\Entity\ClientToken;
use Shared\TokenBundle\Service\ClientTokenService;

/**
 * Class TokenAuthenticator
 * @package Shared\AuthBundle\Service\Security\Authenticator\Client
 *
 * @DI\Service("shared.auth.service.security.authenticator.client.token", public=true)
 */
class TokenAuthenticator extends AbstractTokenAuthenticator
{
    /**
     * @param ClientTokenService $clientTokenService
     *
     * @DI\InjectParams({
     *     "clientTokenService" = @DI\Inject("shared.token.service.token.client")
     * })
     */
    public function __construct(ClientTokenService $clientTokenService)
    {
        parent::__construct($clientTokenService);
    }

    /**
     * @param UserProviderInterface $provider
     * @param AbstractToken $token
     * @return bool
     */
    protected function isProviderSupportToken(UserProviderInterface $provider, AbstractToken $token): bool
    {
        return ($token instanceof ClientToken) && $provider->supportsClass($token->getUserRole());
    }

    /**
     * @param string $hash
     * @return bool
     */
    protected function isValidHash(string $hash): bool
    {
        list($publicId, $clientId, $sign) = explode('.', $hash);

        return mb_strlen($publicId) <= 10;
    }
}