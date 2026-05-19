<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use App\Form\CommandeStatutType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/commandes')]
#[IsGranted('ROLE_ADMIN')]
class CommandeCrudController extends AbstractController
{
    #[Route('', name: 'admin_commande_index')]
    public function index(CommandeRepository $repo): Response
    {
        return $this->render('admin/commande/index.html.twig', [
            'commandes' => $repo->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_commande_edit')]
    public function edit(Commande $commande, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CommandeStatutType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Statut de la commande mis à jour.');
            return $this->redirectToRoute('admin_commande_index');
        }

        return $this->render('admin/commande/edit.html.twig', [
            'form'     => $form,
            'commande' => $commande,
        ]);
    }
}
