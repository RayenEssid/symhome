<?php
namespace App\Controller;

use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/panier', name: 'app_panier')]
class PanierController extends AbstractController
{
    public function __construct(private PanierService $panierService) {}

    #[Route('', name: '')]
    public function index(): Response
    {
        return $this->render('panier/index.html.twig', [
            'panier' => $this->panierService->getPanierAvecDonnees(),
        ]);
    }

    #[Route('/ajouter/{id}', name: '_ajouter', methods: ['POST'])]
    public function ajouter(int $id, Request $request): Response
    {
        $quantite = max(1, (int) $request->request->get('quantite', 1));
        $this->panierService->ajouter($id, $quantite);
        $this->addFlash('success', 'Article ajouté au panier !');
        return $this->redirect($request->headers->get('referer', $this->generateUrl('app_panier')));
    }

    #[Route('/modifier/{id}', name: '_modifier', methods: ['POST'])]
    public function modifier(int $id, Request $request): Response
    {
        $quantite = (int) $request->request->get('quantite', 1);
        $this->panierService->modifier($id, $quantite);
        return $this->redirectToRoute('app_panier');
    }

    #[Route('/supprimer/{id}', name: '_supprimer', methods: ['POST'])]
    public function supprimer(int $id): Response
    {
        $this->panierService->supprimer($id);
        $this->addFlash('info', 'Article retiré du panier.');
        return $this->redirectToRoute('app_panier');
    }

    #[Route('/vider', name: '_vider', methods: ['POST'])]
    public function vider(): Response
    {
        $this->panierService->vider();
        $this->addFlash('info', 'Panier vidé.');
        return $this->redirectToRoute('app_panier');
    }
}