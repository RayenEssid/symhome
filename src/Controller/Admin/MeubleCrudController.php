<?php

namespace App\Controller\Admin;

use App\Entity\Meuble;
use App\Form\MeubleType;
use App\Repository\MeubleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/meubles')]
#[IsGranted('ROLE_ADMIN')]
class MeubleCrudController extends AbstractController
{
    #[Route('', name: 'admin_meuble_index')]
    public function index(MeubleRepository $repo): Response
    {
        return $this->render('admin/meuble/index.html.twig', [
            'meubles' => $repo->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_meuble_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $meuble = new Meuble();
        $form = $this->createForm(MeubleType::class, $meuble);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($meuble);
            $em->flush();
            $this->addFlash('success', 'Meuble ajouté avec succès.');
            return $this->redirectToRoute('admin_meuble_index');
        }

        return $this->render('admin/meuble/form.html.twig', [
            'form'  => $form,
            'titre' => 'Nouveau meuble',
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_meuble_edit')]
    public function edit(Meuble $meuble, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(MeubleType::class, $meuble);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Meuble modifié avec succès.');
            return $this->redirectToRoute('admin_meuble_index');
        }

        return $this->render('admin/meuble/form.html.twig', [
            'form'   => $form,
            'titre'  => 'Modifier : ' . $meuble->getNom(),
            'meuble' => $meuble,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_meuble_delete', methods: ['POST'])]
    public function delete(Meuble $meuble, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete-meuble-' . $meuble->getId(), $request->request->get('_token'))) {
            $em->remove($meuble);
            $em->flush();
            $this->addFlash('success', 'Meuble supprimé.');
        }
        return $this->redirectToRoute('admin_meuble_index');
    }
}
