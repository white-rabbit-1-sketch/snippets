<?php

namespace Shared\AuthBundle\Service\Security\Authenticator\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\TokenBundle\Entity\UserToken;
use Shared\TokenBundle\Entity\AbstractToken;
use Shared\TokenBundle\Service\UserTokenService;
use Shared\AuthBundle\Service\Security\Authenticator\AbstractTokenAuthenticator;

/**
 * Class TokenAuthenticator
 * @package Shared\AuthBundle\Service\Security\Authenticator\User
 *
 * @DI\Service("shared.auth.service.security.authenticator.user.token", public=true)
 */
class TokenAuthenticator extends AbstractTokenAuthenticator
{
    /**
     * @param UserTokenService $userTokenService
     *
     * @DI\InjectParams({
     *     "userTokenService" = @DI\Inject("shared.token.service.token.user")
     * })
     */
    public function __construct(UserTokenService $userTokenService)
    {
        parent::__construct($userTokenService);
    }

    /**
     * @param UserProviderInterface $provider
     * @param AbstractToken $token
     * @return bool
     */
    protected function isProviderSupportToken(UserProviderInterface $provider, AbstractToken $token): bool
    {
        return ($token instanceof UserToken) && $provider->supportsClass($token->getUserRole());
    }

    /**
     * @param string $hash
     * @return bool
     */
    protected function isValidHash(string $hash): bool
    {
        $result = false;

        $hashPartList = explode('.', $hash);
        if (count($hashPartList) == 3) {
            list($alg, $data, $sign) = $hashPartList;

            $result = mb_strlen($alg) > 10;
        }

        return $result;
    }
}