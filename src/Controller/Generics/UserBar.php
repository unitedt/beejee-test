<?php

namespace App\Controller\Generics;

use App\Form\Type\LoginType;

trait UserBar {
    /**
     * @return array
     */
    public function getViewContext(): array {
        $context = parent::getViewContext();
        $context['loginForm'] = $this->formFactory->create(LoginType::class, [])->createView();
        return $context;
    }
}
