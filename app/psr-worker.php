<?php
/**
 * @var Goridge\RelayInterface $relay
 */
use Spiral\Goridge;
use Spiral\RoadRunner;
use DI\ContainerBuilder;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

ini_set('display_errors', 'stderr');
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Message interfaces for worker:
$worker = new RoadRunner\Worker(new Goridge\StreamRelay(STDIN, STDOUT));
$psr7 = new RoadRunner\PSR7Client($worker);
$httpFoundationFactory = new HttpFoundationFactory();
$psr7Factory = new PsrHttpFactory(
    new RoadRunner\Diactoros\ServerRequestFactory(),
    new RoadRunner\Diactoros\StreamFactory(),
    new RoadRunner\Diactoros\UploadedFileFactory(),
    new \Laminas\Diactoros\ResponseFactory()
);

// Build Dependency Injection container with main application components:
try {
    $containerBuilder = new ContainerBuilder;
    $containerBuilder->addDefinitions(__DIR__ . '/bootstrap.php');
    $container = $containerBuilder->build();
} catch (\Throwable $e) {
    $psr7->getWorker()->error((string)$e);
}

// Serve web requests to app:
while ($req = $psr7->acceptRequest()) {
    try {
        // heartbeat
        $conn = $container->get(\Doctrine\DBAL\Connection::class);
        if (false === $conn->ping()) {
            $conn->close();
            $conn->connect();
        }

        $request = $httpFoundationFactory->createRequest($req);

        // session
        if ($request->cookies->has(session_name())) {
            session_id($request->cookies->get(session_name()));
        }

        $session = new Session();
        $request->setSession($session);

        // update token storage
        $tokenStorage = $container->get(TokenStorageInterface::class);
        $tokenGenerator = new UriSafeTokenGenerator();
        $token = $request->getSession()->get('_security_');
        $tokenStorage->setToken($token ?? new AnonymousToken($tokenGenerator->generateToken(), 'anonymous'));

        // update Csrf Token manager based on session
        $container->set(CsrfTokenManager::class, function () use ($request) {
            return new CsrfTokenManager(null, new SessionTokenStorage($request->getSession()));
        });

        $twig = $container->get(\Twig\Environment::class);
        $twig->addGlobal('request', $request);

        $dispatcher = $container->get(FastRoute\Dispatcher::class);
        $route = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

        /**
         * @var \Psr\Http\Message\ResponseInterface $psr7resp
         */
        $psr7resp = new \Zend\Diactoros\Response();
        $response = $httpFoundationFactory->createResponse($psr7resp);

        switch ($route[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                $response->setStatusCode(404);
                $response->setContent('Not Found');
                break;

            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $response->setStatusCode(405);
                $response->setContent('Method Not Allowed');
                break;

            case FastRoute\Dispatcher::FOUND:
                list(, $controller, $parameters) = $route;
                $parameters['_request'] = $request;
                $body = $container->call($controller, $parameters);

                if ($body instanceof RedirectResponse) {
                    $response->setStatusCode(302);
                    $response->setContent($body->getContent());

                } else {
                    $response->setContent($body);
                }

                break;
        }

        // write token
        $token = $tokenStorage->getToken();
        $request->getSession()->set('_security_', $token);

        $response->headers->setCookie(
            new \Symfony\Component\HttpFoundation\Cookie(
                session_name(),
                session_id(),
                0,
                '/',
                '',
                false,
                true,
                false,
                'lax'
            )
        );

        $psr7->respond($psr7Factory->createResponse($response));

    } catch (\Symfony\Component\Security\Core\Exception\AccessDeniedException $e) {
        $flash = new \App\FlashMessage($request->getSession());
        $flash->error($e->getMessage());

        $response = new RedirectResponse($container->get('app.root_uri'));
        $psr7->respond($psr7Factory->createResponse($response));

    } catch (\Throwable $e) {
        $psr7->getWorker()->error((string)$e);

    } finally {
        if(PHP_SESSION_ACTIVE === session_status()) {
            session_write_close();
        }
        session_id('');
        session_unset();
        unset($_SESSION);

    }

}