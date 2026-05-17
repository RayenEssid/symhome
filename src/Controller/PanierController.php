<?php

namespace App\Controller;

use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/panier')]
class PanierController extends AbstractController
{
    public function __construct(private PanierService $panierService) {}

    #[Route('/', name: 'app_panier_index')]
    public function index(): Response
    {
        return $this->render('panier/index.html.twig', [
            'items' => $this->panierService->getPanierComplet(),
            'total' => $this->panierService->getTotal(),
        ]);
    }

    #[Route('/ajouter/{id}', name: 'app_panier_ajouter', methods: ['POST'])]
    public function ajouter(int $id, Request $request): Response
    {
        $quantite = max(1, (int) $request->request->get('quantite', 1));
        $this->panierService->ajouterArticle($id, $quantite);
        $this->addFlash('success', 'Article ajouté au panier !');
        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/modifier/{id}', name: 'app_panier_modifier', methods: ['POST'])]
    public function modifier(int $id, Request $request): Response
    {
        $quantite = (int) $request->request->get('quantite', 1);
        $this->panierService->modifierQuantite($id, $quantite);
        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/supprimer/{id}', name: 'app_panier_supprimer')]
    public function supprimer(int $id): Response
    {
        $this->panierService->supprimerArticle($id);
        $this->addFlash('info', 'Article retiré du panier.');
        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/vider', name: 'app_panier_vider')]
    public function vider(): Response
    {
        $this->panierService->viderPanier();
        $this->addFlash('info', 'Panier vidé.');
        return $this->redirectToRoute('app_panier_index');
    }
}