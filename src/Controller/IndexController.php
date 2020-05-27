<?php

namespace App\Controller;

use App\Controller\Generics\GenericController;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Environment;
use App\Controller\Generics\UserBar;

class IndexController extends GenericController
{
    use UserBar;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * IndexController constructor.
     * @param Environment $twig
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(Environment $twig, FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
        $this->twig = $twig;
    }

    /**
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function __invoke()
    {
        return $this->twig->render('index.twig', $this->getViewContext());
    }
}
