<?php

namespace App\Controller;

use App\Controller\Generics\GenericController;
use App\Controller\Generics\UserBar;
use App\FlashMessage;
use App\Form\Type\TaskAddType;
use App\Form\Type\TaskEditType;
use App\Repository\TaskRepositoryInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Environment;
use App\Model\Task;

class TaskController extends GenericController
{
    use UserBar;

    /**
     * @var string
     */
    private $rootUri;

    /**
     * @var TaskRepositoryInterface
     */
    private $repository;

    /**
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authChecker;

    /**
     * TaskController constructor.
     * @param ContainerInterface $container
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->rootUri = $container->get('app.root_uri');
        $this->repository = $container->get(TaskRepositoryInterface::class);
        $this->twig = $container->get(Environment::class);
        $this->formFactory = $container->get(FormFactoryInterface::class);
        $this->authChecker = $container->get(AuthorizationCheckerInterface::class);
    }

    /**
     * @param Request $_request
     * @return string|RedirectResponse
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws LogicException
     * @throws \InvalidArgumentException
     */
    public function add(Request $_request)
    {
        $task = new Task();

        $form = $this->formFactory->create(TaskAddType::class, $task);

        $handler = new HttpFoundationRequestHandler();
        $handler->handleRequest($form, $_request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->save($task);

            $flash = new FlashMessage($_request->getSession());
            $flash->success('Task added');

            return new RedirectResponse($this->rootUri);
        }

        return $this->twig->render('task.twig', $this->getViewContext() + [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param int $id
     * @param Request $_request
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws InvalidOptionsException
     * @throws LogicException
     * @throws \InvalidArgumentException
     * @throws AccessDeniedException
     */
    public function edit(int $id, Request $_request)
    {
        if (!$this->authChecker->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Only admin can edit task!');
        }

        $task = $this->repository->findById($id);
        $oldContent = $task->getContent();

        $form = $this->formFactory->create(TaskEditType::class, $task);

        $handler = new HttpFoundationRequestHandler();
        $handler->handleRequest($form, $_request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($oldContent !== $task->getContent()) {
                $task->setIsChangedByAdmin(true);
            }

            $this->repository->save($task);

            $flash = new FlashMessage($_request->getSession());
            $flash->success('Task updated');

            return new RedirectResponse($this->rootUri);
        }

        return $this->twig->render('task.twig', $this->getViewContext() + [
            'form' => $form->createView(),
        ]);
    }
}