<?php
namespace App\Service;

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

    /** Retourne le panier brut : ['id_meuble' => quantite, ...] */
    public function getPanier(): array
    {
        return $this->getSession()->get('panier', []);
    }

    /** Ajoute 1 unité ou quantité précise */
    public function ajouter(int $id, int $quantite = 1): void
    {
        $panier = $this->getPanier();
        $panier[$id] = ($panier[$id] ?? 0) + $quantite;
        $this->getSession()->set('panier', $panier);
    }

    /** Modifie la quantité (0 = supprime) */
    public function modifier(int $id, int $quantite): void
    {
        $panier = $this->getPanier();
        if ($quantite <= 0) {
            unset($panier[$id]);
        } else {
            $panier[$id] = $quantite;
        }
        $this->getSession()->set('panier', $panier);
    }

    /** Supprime un article */
    public function supprimer(int $id): void
    {
        $panier = $this->getPanier();
        unset($panier[$id]);
        $this->getSession()->set('panier', $panier);
    }

    /** Vide le panier */
    public function vider(): void
    {
        $this->getSession()->remove('panier');
    }

    /** Retourne les données complètes : meubles + quantités + total */
    public function getPanierAvecDonnees(): array
    {
        $panier = $this->getPanier();
        $data   = [];
        $total  = 0;

        foreach ($panier as $id => $quantite) {
            $meuble = $this->meubleRepository->find($id);
            if (!$meuble) continue;

            $sousTotal = $meuble->getPrix() * $quantite;
            $total    += $sousTotal;

            $data[] = [
                'meuble'    => $meuble,
                'quantite'  => $quantite,
                'sousTotal' => $sousTotal,
            ];
        }

        return ['items' => $data, 'total' => $total];
    }

    /** Nombre total d'articles (pour le badge navbar) */
    public function getNombreArticles(): int
    {
        return array_sum($this->getPanier());
    }
}