<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class LoginType extends AbstractType {
	/**
	 * @param FormBuilderInterface $builder
	 * @param array                $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		parent::buildForm($builder, $options);

		$builder
			->add('_username', TextType::class, array('label' => 'Username'))
			->add('_password', PasswordType::class, array('label' => 'Password'))
			->add('submit', SubmitType::class, array('label' => 'Login'))
		;
	}

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver) {
		parent::configureOptions($resolver);
// 		$resolver->setDefaults(array(
// 			'data_class' => User::class,
// 			'csrf_protection' => true,
// 			'csrf_field_name' => '_token',
// 		));
	}

	public function getName() {
		return 'login';
	}
}
