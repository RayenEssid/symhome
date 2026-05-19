<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Form\AdresseType;
use App\Repository\CommandeRepository;
use App\Repository\MeubleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/commande')]
#[IsGranted('ROLE_USER')]
class CommandeController extends AbstractController
{
    public function __construct(
        private RequestStack          $requestStack,
        private MeubleRepository      $meubleRepository,
        private EntityManagerInterface $em,
        private CommandeRepository     $commandeRepository,
    ) {}

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }

    private function getPanier(): array
    {
        return $this->getSession()->get('panier', []);
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

    private function viderPanier(): void
    {
        $this->getSession()->remove('panier');
    }

    private function estVide(): bool
    {
        return empty($this->getPanier());
    }

    #[Route('/checkout', name: 'app_commande_checkout')]
    public function checkout(Request $request): Response
    {
        if ($this->estVide()) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_panier_index');
        }

        $commande = new Commande();
        $form = $this->createForm(AdresseType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commande->setUser($this->getUser());

            $items = $this->getPanierComplet();
            foreach ($items as $item) {
                $ligne = new LigneCommande();
                $ligne->setMeuble($item['meuble']);
                $ligne->setQuantite($item['quantite']);
                $ligne->setPrixUnitaire($item['meuble']->getPrix());
                $commande->addLigneCommande($ligne);
                $this->em->persist($ligne);
            }

            $commande->calculerTotal();
            $commande->setStatut(Commande::STATUT_EN_ATTENTE);
            $this->em->persist($commande);
            $this->em->flush();

            // Décrémenter le stock
            foreach ($commande->getLignesCommande() as $ligne) {
                $meuble = $ligne->getMeuble();
                $meuble->setStock(max(0, $meuble->getStock() - $ligne->getQuantite()));
            }
            $this->em->flush();

            $this->viderPanier();
            $this->addFlash('success', 'Commande passée avec succès ! Nous vous contacterons pour confirmer la livraison.');
            return $this->redirectToRoute('app_commande_success', ['id' => $commande->getId()]);
        }

        return $this->render('commande/checkout.html.twig', [
            'form'  => $form,
            'items' => $this->getPanierComplet(),
            'total' => $this->getTotal(),
        ]);
    }

    #[Route('/success/{id}', name: 'app_commande_success')]
    public function success(Commande $commande): Response
    {
        if ($commande->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('commande/success.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/historique', name: 'app_commande_historique')]
    public function historique(): Response
    {
        $commandes = $this->commandeRepository->findBy(
            ['user' => $this->getUser()],
            ['createdAt' => 'DESC']
        );

        return $this->render('commande/historique.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/detail/{id}', name: 'app_commande_detail')]
    public function detail(Commande $commande): Response
    {
        if ($commande->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('commande/detail.html.twig', [
            'commande' => $commande,
        ]);
    }
}
