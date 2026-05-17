<?php

namespace App\Service;

use App\Entity\Meuble;
use App\Repository\MeubleRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class PanierService
{
    public function __construct(
        private RequestStack $requestStack,
        private MeubleRepository $meubleRepository
    ) {}

    private function getSession()
    {
        return $this->requestStack->getSession();
    }

    public function getPanier(): array
    {
        return $this->getSession()->get('panier', []);
    }

    public function ajouterArticle(int $meubleId, int $quantite = 1): void
    {
        $panier = $this->getPanier();
        if (isset($panier[$meubleId])) {
            $panier[$meubleId] += $quantite;
        } else {
            $panier[$meubleId] = $quantite;
        }
        $this->getSession()->set('panier', $panier);
    }

    public function modifierQuantite(int $meubleId, int $quantite): void
    {
        $panier = $this->getPanier();
        if ($quantite <= 0) {
            unset($panier[$meubleId]);
        } else {
            $panier[$meubleId] = $quantite;
        }
        $this->getSession()->set('panier', $panier);
    }

    public function supprimerArticle(int $meubleId): void
    {
        $panier = $this->getPanier();
        unset($panier[$meubleId]);
        $this->getSession()->set('panier', $panier);
    }

    public function viderPanier(): void
    {
        $this->getSession()->remove('panier');
    }

    public function getPanierComplet(): array
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

    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->getPanierComplet() as $item) {
            $total += $item['sousTotal'];
        }
        return $total;
    }

    public function getNombreArticles(): int
    {
        return array_sum($this->getPanier());
    }

    public function estVide(): bool
    {
        return empty($this->getPanier());
    }
}