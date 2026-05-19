<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LegacyController extends AbstractController
{
    #[Route('/meuble', name: 'app_meuble_alias')]
    public function meubleIndex(): Response
    {
        return $this->forward(MeubleController::class . '::index');
    }

    #[Route('/registration', name: 'app_register_alias')]
    public function registration(): Response
    {
        return $this->forward(RegistrationController::class . '::register');
    }
}
