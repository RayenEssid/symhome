<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Repository\CommandeRepository;
use App\Repository\MeubleRepository;
use App\Service\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/commande')]
#[IsGranted('ROLE_USER')]
class CommandeController extends AbstractController
{
    public function __construct(
        private PanierService $panierService,
        private EntityManagerInterface $em,
        private MeubleRepository $meubleRepository,
        private CommandeRepository $commandeRepository,
    ) {}

    #[Route('/checkout', name: 'app_commande_checkout')]
    public function checkout(): Response
    {
        if ($this->panierService->estVide()) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_panier_index');
        }

        $items = $this->panierService->getPanierComplet();

        return $this->render('commande/checkout.html.twig', [
            'items' => $items,
            'total' => $this->panierService->getTotal(),
        ]);
    }

    #[Route('/paiement', name: 'app_commande_paiement', methods: ['POST'])]
    public function paiement(Request $request): Response
    {
        if ($this->panierService->estVide()) {
            return $this->redirectToRoute('app_panier_index');
        }

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $items = $this->panierService->getPanierComplet();
        $lineItems = [];

        foreach ($items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency'     => 'eur',
                    'product_data' => ['name' => $item['meuble']->getNom()],
                    'unit_amount'  => (int) round((float) $item['meuble']->getPrix() * 100),
                ],
                'quantity' => $item['quantite'],
            ];
        }

        // Créer la commande en BDD avec statut en_attente
        $commande = new Commande();
        $commande->setUser($this->getUser());

        foreach ($items as $item) {
            $ligne = new LigneCommande();
            $ligne->setMeuble($item['meuble']);
            $ligne->setQuantite($item['quantite']);
            $ligne->setPrixUnitaire($item['meuble']->getPrix());
            $commande->addLigneCommande($ligne);
            $this->em->persist($ligne);
        }
        $commande->calculerTotal();
        $this->em->persist($commande);
        $this->em->flush();

        // Session Stripe
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => $lineItems,
            'mode'                 => 'payment',
            'success_url'          => $this->generateUrl('app_commande_success', ['id' => $commande->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url'           => $this->generateUrl('app_commande_cancel', ['id' => $commande->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'metadata'             => ['commande_id' => $commande->getId()],
        ]);

        $commande->setStripeSessionId($session->id);
        $this->em->flush();

        return $this->redirect($session->url, 303);
    }

    #[Route('/success/{id}', name: 'app_commande_success')]
    public function success(Commande $commande): Response
    {
        if ($commande->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        // Mettre à jour le statut et décrémenter le stock
        if ($commande->getStatut() === Commande::STATUT_EN_ATTENTE) {
            $commande->setStatut(Commande::STATUT_COMPLETEE);

            foreach ($commande->getLignesCommande() as $ligne) {
                $meuble = $ligne->getMeuble();
                $newStock = max(0, $meuble->getStock() - $ligne->getQuantite());
                $meuble->setStock($newStock);
            }

            $this->em->flush();
            $this->panierService->viderPanier();
        }

        return $this->render('commande/success.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/cancel/{id}', name: 'app_commande_cancel')]
    public function cancel(Commande $commande): Response
    {
        if ($commande->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $commande->setStatut(Commande::STATUT_ANNULEE);
        $this->em->flush();

        $this->addFlash('warning', 'Paiement annulé. Votre commande a été annulée.');
        return $this->redirectToRoute('app_panier_index');
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