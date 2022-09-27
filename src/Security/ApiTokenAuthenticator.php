<?php

namespace App\Security;

use App\Repository\AgentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    private const HEADER_API_TOKEN = 'x-api-token';
    private AgentRepository $agentRepository;

    public function __construct(AgentRepository $agentRepository)
    {
        $this->agentRepository = $agentRepository;
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has(self::HEADER_API_TOKEN);
    }

    public function authenticate(Request $request): Passport
    {
        $apiToken = $request->headers->get(self::HEADER_API_TOKEN);

        if (null === $apiToken) {
            throw new CustomUserMessageAuthenticationException('Auth token not found (header: "{{ header }}")', [
                '{{ header }}' => self::HEADER_API_TOKEN,
            ]);
        }

        return new SelfValidatingPassport(
            new UserBadge($apiToken, function ($apiToken) {
                $agent = $this->agentRepository->findByApiToken($apiToken);

                if (!$agent) {
                    throw new UserNotFoundException();
                }

                return $agent;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        throw $exception;
    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        /*
//         * If you would like this class to control what happens when an anonymous user accesses a
//         * protected page (e.g. redirect to /login), uncomment this method and make this class
//         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
//         *
//         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
//         */
//    }
}
