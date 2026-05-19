<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\MeubleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[Route('/home', name: 'app_home_alias')]
    public function index(
        CategorieRepository $categorieRepository,
        MeubleRepository $meubleRepository
    ): Response {
        return $this->render('home/index.html.twig', [
            'categories'      => $categorieRepository->findAll(),
            'meubles_vedette' => $meubleRepository->findBy([], ['createdAt' => 'DESC'], 8),
        ]);
    }
}
