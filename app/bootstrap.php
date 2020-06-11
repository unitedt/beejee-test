<?php

use function DI\create;
use App\Repository\TaskRepositoryInterface;
use App\Repository\TaskRepository;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use FastRoute\RouteCollector;
use Doctrine\DBAL\DriverManager;
use Psr\Container\ContainerInterface;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Bridge\Twig\Extension\SecurityExtension;

return [
    'app.root_uri' => DI\env('APP_ROOT_URI', 'http://localhost:8080'),
    'db.name'      => DI\env('DB_NAME','beejee_test'),
    'db.user'      => DI\env('DB_USER','root'),
    'db.password'  => DI\env('DB_PASSWORD','r56t'),
    'db.host'      => DI\env('DB_HOST','127.0.0.1'),
    'db.port'      => DI\env('DB_PORT',3307),

    // Configure Router
    FastRoute\Dispatcher::class => function() {
        return FastRoute\simpleDispatcher(function (RouteCollector $r) {
            $r->addRoute('GET', '/', 'App\Controller\IndexController');
            $r->addRoute('POST', '/login', ['App\Controller\AuthController', 'login']);
            $r->addRoute('GET', '/logout', ['App\Controller\AuthController', 'logout']);
            $r->addRoute(['GET', 'POST'], '/task', ['App\Controller\TaskController', 'add']);
            $r->addRoute(['GET', 'POST'], '/task/{id}', ['App\Controller\TaskController', 'edit']);
            $r->addRoute('GET', '/api/tasks', 'App\Controller\Api\TaskController');
        });
    },

    // Configure Repositories
    TaskRepositoryInterface::class => function(ContainerInterface $c) {
        return new TaskRepository($c->get(Doctrine\DBAL\Connection::class));
    },

    // Configure DB
    Doctrine\DBAL\Connection::class => function(ContainerInterface $c) {
        $connectionParams = [
            'dbname'   => $c->get('db.name'),
            'user'     => $c->get('db.user'),
            'password' => $c->get('db.password'),
            'host'     => $c->get('db.host'),
            'port'     => $c->get('db.port'),
            'charset' => 'utf8',
            'driver'   => 'pdo_mysql',
        ];

        return DriverManager::getConnection($connectionParams);
    },

    // Configure User Provider
    UserProviderInterface::class => function() {
        return new InMemoryUserProvider(
            [
                'admin' => [
                    'password' => '123',
                    'roles' => ['ROLE_ADMIN'],
                ],
            ]
        );
    },

    // Configure Authentication Manager
    AuthenticationManagerInterface::class => function(ContainerInterface $c) {
        // Create an encoder factory that will "encode" passwords
        $encoderFactory = new \Symfony\Component\Security\Core\Encoder\EncoderFactory([
            // We simply use plaintext passwords for users from this specific class
            Symfony\Component\Security\Core\User\User::class => new PlaintextPasswordEncoder(),
        ]);

        // The user checker is a simple class that allows to check against different elements (user disabled, account expired etc)
        $userChecker = new UserChecker();

        // The (authentication) providers are a way to make sure to match credentials against users based on their "providerkey".
        $providers = array(
            new DaoAuthenticationProvider($c->get(UserProviderInterface::class), $userChecker, 'main', $encoderFactory, true),
        );

        return new AuthenticationProviderManager($providers, true);
    },

    // Configure Token Storage
    TokenStorageInterface::class => function() {
        // We store our (authenticated) token inside the token storage
        return new TokenStorage();
    },

    // Configure Authorization Checker
    AuthorizationCheckerInterface::class => function(ContainerInterface $c) {
        // We only create a single voter that checks on given token roles.
        $voters = array(
            new \Symfony\Component\Security\Core\Authorization\Voter\RoleVoter('ROLE_'),
        );

        // Tie all voters into the access decision manager (
        $accessDecisionManager = new AccessDecisionManager(
            $voters,
            AccessDecisionManager::STRATEGY_AFFIRMATIVE,
            false,
            true
        );

        return new AuthorizationChecker(
            $c->get(TokenStorageInterface::class),
            $c->get(AuthenticationManagerInterface::class),
            $accessDecisionManager,
            false
        );
    },

    // Configure CSRF Token Manager
    CsrfTokenManager::class => function() {
        return new CsrfTokenManager();
    },

    // Configure Validator component
    Validation::class => function() {
        return Validation::createValidator();
    },

    // Configure Translator
    Translator::class => function() {
        return new Translator('en');
    },

    // Configure Form factory
    FormFactoryInterface::class => function(ContainerInterface $c) {
        return Forms::createFormFactoryBuilder()
            ->addExtension(new CsrfExtension($c->get(CsrfTokenManager::class)))
            ->addExtension(new ValidatorExtension($c->get(Validation::class)))
            ->getFormFactory();
    },

    // Configure Twig
    Environment::class => function(ContainerInterface $c) {
        $rootDir = dirname(__DIR__);

        // the Twig file that holds all the default markup for rendering forms
        // this file comes with TwigBridge
        $defaultFormTheme = 'bootstrap_4_horizontal_layout.html.twig';

        // the path to TwigBridge library so Twig can locate the
        // form_div_layout.html.twig file
        $appVariableReflection = new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
        $vendorTwigBridgeDirectory = dirname($appVariableReflection->getFileName());

        $loader = new FilesystemLoader([
            $rootDir . '/templates',
            $vendorTwigBridgeDirectory . '/Resources/views/Form',
        ]);

        $twig = new Environment($loader, [
            'cache' => $rootDir . '/var/cache',
            'auto_reload' => true, // for debug
        ]);

        $formEngine = new TwigRendererEngine([$defaultFormTheme], $twig);

        $twig->addRuntimeLoader(new FactoryRuntimeLoader([
            FormRenderer::class => function () use ($formEngine, $c) {
                return new FormRenderer($formEngine, $c->get(CsrfTokenManager::class));
            },
        ]));

        $twig->addExtension(new SecurityExtension($c->get(AuthorizationCheckerInterface::class)));
        $twig->addExtension(new TranslationExtension($c->get(Translator::class)));
        $twig->addExtension(new FormExtension());
        $twig->addGlobal('container', $c);

        return $twig;
    },
];