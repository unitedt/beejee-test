<?php

namespace App\Controller;

use App\FlashMessage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthController
{
    /**
     * @var string
     */
    private $rootUri;

    /**
     * @var AuthenticationManagerInterface
     */
    private $authManager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * AuthController constructor.
     * @param ContainerInterface $container
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->rootUri = $container->get('app.root_uri');
        $this->authManager = $container->get(AuthenticationManagerInterface::class);
        $this->tokenStorage = $container->get(TokenStorageInterface::class);
    }

    /**
     * @param Request $_request
     * @return RedirectResponse
     * @throws \InvalidArgumentException
     */
    public function login(Request $_request)
    {
        try {
            $token = new UsernamePasswordToken(
                $_request->request->get('login')['_username'] ?? '',
                $_request->request->get('login')['_password'] ?? '',
                'main',
                []
            );

            $token = $this->authManager->authenticate($token);

            // Store "authenticated" token in the token storage
            $this->tokenStorage->setToken($token);

            $flash = new FlashMessage($_request->getSession());
            $flash->success('Welcome!');

        } catch (AuthenticationException $e) {
            $flash = new FlashMessage($_request->getSession());
            $flash->error($e->getMessage());

        }

        return new RedirectResponse($this->rootUri);
    }


    public function logout(Request $_request)
    {
        $this->tokenStorage->setToken(null);

        $flash = new FlashMessage($_request->getSession());
        $flash->success('You logged out!');
        return new RedirectResponse($this->rootUri);
    }
}
