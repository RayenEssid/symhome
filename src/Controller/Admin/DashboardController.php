<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Repository\MeubleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractController
{
    public function __construct(
        private CommandeRepository $commandeRepository,
        private MeubleRepository   $meubleRepository,
        private UserRepository     $userRepository
    ) {}

    #[Route('', name: 'admin_dashboard')]
    public function index(): Response
    {
        $statsCommandes  = $this->commandeRepository->getStatsMensuelles();
        $commandesRecentes = $this->commandeRepository->findBy([], ['createdAt' => 'DESC'], 5);
        $totalCA         = $this->commandeRepository->getTotalCA();
        $nbCommandes     = $this->commandeRepository->count(['statut' => Commande::STATUT_COMPLETEE]);
        $nbMeubles       = $this->meubleRepository->count([]);
        $nbUtilisateurs  = $this->userRepository->count([]);

        return $this->render('admin/dashboard.html.twig', [
            'stats_commandes'    => $statsCommandes,
            'commandes_recentes' => $commandesRecentes,
            'total_ca'           => $totalCA,
            'nb_commandes'       => $nbCommandes,
            'nb_meubles'         => $nbMeubles,
            'nb_utilisateurs'    => $nbUtilisateurs,
        ]);
    }
}
