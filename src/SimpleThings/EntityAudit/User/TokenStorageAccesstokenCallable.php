<?php

namespace SimpleThings\EntityAudit\User;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authentication\Token\SwitchUserToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Trikoder\Bundle\OAuth2Bundle\Security\Authentication\Token\OAuth2Token;

class TokenStorageAccesstokenCallable
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return string|null
     */
    public function __invoke()
    {
        $oauth2TokenClass = "Trikoder\Bundle\OAuth2Bundle\Security\Authentication\Token\OAuth2Token";
        /** @var TokenInterface $token */
        $token = $this->container->get('security.token_storage')->getToken();
        if (null !== $token && $token->isAuthenticated()) {
            if ($token instanceof $oauth2TokenClass) {
                return $token->getCredentials();
            } else {
                return null;
            }
        }
    }
}
