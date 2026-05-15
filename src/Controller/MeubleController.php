<?php
namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\MeubleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/catalogue', name: 'app_meuble_')]
class MeubleController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(
        Request $request,
        MeubleRepository $meubleRepo,
        CategorieRepository $categorieRepo
    ): Response {
        $search    = $request->query->get('q', '');
        $categorieId = $request->query->get('categorie');

        $meubles    = $meubleRepo->search($search, $categorieId);
        $categories = $categorieRepo->findAll();

        return $this->render('meuble/index.html.twig', [
            'meubles'    => $meubles,
            'categories' => $categories,
            'search'     => $search,
            'categorieId' => $categorieId,
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show(int $id, MeubleRepository $meubleRepo): Response
    {
        $meuble = $meubleRepo->find($id);
        if (!$meuble) {
            throw $this->createNotFoundException('Meuble introuvable');
        }

        return $this->render('meuble/show.html.twig', ['meuble' => $meuble]);
    }
}