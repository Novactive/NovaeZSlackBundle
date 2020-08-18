<?php
/**
 * NovaeZSlackBundle Bundle.
 *
 * @package   Novactive\Bundle\eZSlackBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZSlackBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZSlackBundle\Security;

use eZ\Publish\Core\MVC\Symfony\Security\UserInterface;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Novactive\Bundle\eZSlackBundle\Core\Converter\User as UserConverter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class SlackAuthenticator.
 */
class SlackAuthenticator extends SocialAuthenticator
{
    /**
     * @var ClientRegistry
     */
    private $clientRegistry;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var UserConverter
     */
    private $userConverter;

    /**
     * SlackAuthenticator constructor.
     *
     * @param ClientRegistry  $clientRegistry
     * @param RouterInterface $router
     * @param UserConverter   $user
     */
    public function __construct(ClientRegistry $clientRegistry, RouterInterface $router, UserConverter $user)
    {
        $this->clientRegistry = $clientRegistry;
        $this->router         = $router;
        $this->userConverter  = $user;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request): bool
    {
//        $routePattern = $this->router->generate('_novaezslack_slack_oauth_check');

        $routePattern = '_novaezslack/auth/check';
        // need to manage Site Access here, then we check only the end
        return substr($request->getPathInfo(), -\strlen($routePattern)) === $routePattern;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getClient());
    }

    /**
     * @param mixed                 $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return UserInterface
     */
    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface
    {
        /** @var \eZ\Publish\Core\MVC\Symfony\Security\User\Provider $userProvider */
        $user = $userProvider->loadUserByUsername(
            $this->userConverter->convert($this->getClient()->fetchUserFromToken($credentials))->login
        );
        $userProvider->refreshUser($user);

        return $user;
    }

    /**
     * @return OAuth2Client
     */
    private function getClient(): OAuth2Client
    {
        return $this->clientRegistry->getClient('slack');
    }

    /**
     * Returns a response that directs the user to authenticate.
     *
     * @param Request                 $request       The request that resulted in an AuthenticationException
     * @param AuthenticationException $authException The exception that started the authentication process
     *
     * @return Response
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse($this->router->generate('login'));
    }

    /**
     * Called when authentication executed, but failed (e.g. wrong username password).
     *
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new RedirectResponse($this->router->generate('login'));
    }

    /**
     * Called when authentication executed and was successful!
     *
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey The provider (i.e. firewall) key
     *
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): Response
    {
        $siteaccess = $request->attributes->get('siteaccess');
        /** @var SiteAccess $siteaccess */
        if ('admin' === $siteaccess->name) {
            return new RedirectResponse($this->router->generate('ezplatform.dashboard'));
        }

        return new RedirectResponse('/');
    }
}
