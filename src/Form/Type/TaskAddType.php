<?php

namespace App\Form\Type;

use App\Model\Task;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TaskAddType extends TaskTypeAbstract {
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);

        $builder
            ->add('userName', TextType::class)
            ->add('email', EmailType::class)
            ->add('content', TextareaType::class)
            ->add('submit', SubmitType::class)
        ;
    }

}
