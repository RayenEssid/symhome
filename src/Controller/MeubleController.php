<?php

namespace App\Controller;

use App\Entity\Meuble;
use App\Repository\CategorieRepository;
use App\Repository\MeubleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/catalogue')]
class MeubleController extends AbstractController
{
    #[Route('/', name: 'app_meuble_index')]
    public function index(
        Request $request,
        MeubleRepository $meubleRepository,
        CategorieRepository $categorieRepository
    ): Response {
        $search    = $request->query->get('q', '');
        $categorieSlug = $request->query->get('categorie', '');
        $prixMin   = $request->query->get('prix_min');
        $prixMax   = $request->query->get('prix_max');

        // Convertir les strings vides en null
        $prixMin = !empty($prixMin) ? (float) $prixMin : null;
        $prixMax = !empty($prixMax) ? (float) $prixMax : null;

        $meubles   = $meubleRepository->search($search, $categorieSlug, $prixMin, $prixMax);
        $categories = $categorieRepository->findAll();

        return $this->render('meuble/index.html.twig', [
            'meubles'     => $meubles,
            'categories'  => $categories,
            'search'      => $search,
            'categorieSlug' => $categorieSlug,
            'prix_min'    => $prixMin,
            'prix_max'    => $prixMax,
        ]);
    }

    #[Route('/{id}', name: 'app_meuble_show', requirements: ['id' => '\d+'])]
    public function show(Meuble $meuble): Response
    {
        return $this->render('meuble/show.html.twig', [
            'meuble' => $meuble,
        ]);
    }
}
