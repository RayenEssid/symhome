<?php

namespace App\Controller;

use App\Repository\MeubleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/panier')]
class PanierController extends AbstractController
{
    public function __construct(
        private RequestStack $requestStack,
        private MeubleRepository $meubleRepository
    ) {}

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }

    private function getPanier(): array
    {
        return $this->getSession()->get('panier', []);
    }

    private function setPanier(array $panier): void
    {
        $this->getSession()->set('panier', $panier);
    }

    private function ajouterArticle(int $meubleId, int $quantite = 1): void
    {
        $panier = $this->getPanier();
        if (isset($panier[$meubleId])) {
            $panier[$meubleId] += $quantite;
        } else {
            $panier[$meubleId] = $quantite;
        }
        $this->setPanier($panier);
    }

    private function modifierQuantite(int $meubleId, int $quantite): void
    {
        $panier = $this->getPanier();
        if ($quantite <= 0) {
            unset($panier[$meubleId]);
        } else {
            $panier[$meubleId] = $quantite;
        }
        $this->setPanier($panier);
    }

    private function supprimerArticle(int $meubleId): void
    {
        $panier = $this->getPanier();
        unset($panier[$meubleId]);
        $this->setPanier($panier);
    }

    private function viderPanier(): void
    {
        $this->getSession()->remove('panier');
    }

    private function getPanierComplet(): array
    {
        $panier = $this->getPanier();
        $panierComplet = [];
        foreach ($panier as $id => $quantite) {
            $meuble = $this->meubleRepository->find($id);
            if ($meuble) {
                $panierComplet[] = [
                    'meuble'    => $meuble,
                    'quantite'  => $quantite,
                    'sousTotal' => (float) $meuble->getPrix() * $quantite,
                ];
            }
        }
        return $panierComplet;
    }

    private function getTotal(): float
    {
        $total = 0;
        foreach ($this->getPanierComplet() as $item) {
            $total += $item['sousTotal'];
        }
        return $total;
    }

    private function estVide(): bool
    {
        return empty($this->getPanier());
    }

    #[Route('', name: 'app_panier_index')]
    public function index(): Response
    {
        return $this->render('panier/index.html.twig', [
            'items' => $this->getPanierComplet(),
            'total' => $this->getTotal(),
        ]);
    }

    #[Route('/ajouter/{id}', name: 'app_panier_ajouter', methods: ['POST'])]
    public function ajouter(int $id, Request $request): Response
    {
        $quantite = max(1, (int) $request->request->get('quantite', 1));
        $this->ajouterArticle($id, $quantite);
        $this->addFlash('success', 'Article ajouté au panier !');
        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/modifier/{id}', name: 'app_panier_modifier', methods: ['POST'])]
    public function modifier(int $id, Request $request): Response
    {
        $quantite = (int) $request->request->get('quantite', 1);
        $this->modifierQuantite($id, $quantite);
        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/supprimer/{id}', name: 'app_panier_supprimer')]
    public function supprimer(int $id): Response
    {
        $this->supprimerArticle($id);
        $this->addFlash('info', 'Article retiré du panier.');
        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/vider', name: 'app_panier_vider')]
    public function vider(): Response
    {
        $this->viderPanier();
        $this->addFlash('info', 'Panier vidé.');
        return $this->redirectToRoute('app_panier_index');
    }
}
