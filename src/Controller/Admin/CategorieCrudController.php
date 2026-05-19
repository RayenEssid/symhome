<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/categories')]
#[IsGranted('ROLE_ADMIN')]
class CategorieCrudController extends AbstractController
{
    #[Route('', name: 'admin_categorie_index')]
    public function index(CategorieRepository $repo): Response
    {
        return $this->render('admin/categorie/index.html.twig', [
            'categories' => $repo->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_categorie_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($categorie);
            $em->flush();
            $this->addFlash('success', 'Catégorie ajoutée avec succès.');
            return $this->redirectToRoute('admin_categorie_index');
        }

        return $this->render('admin/categorie/form.html.twig', [
            'form'  => $form,
            'titre' => 'Nouvelle catégorie',
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_categorie_edit')]
    public function edit(Categorie $categorie, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Catégorie modifiée avec succès.');
            return $this->redirectToRoute('admin_categorie_index');
        }

        return $this->render('admin/categorie/form.html.twig', [
            'form'      => $form,
            'titre'     => 'Modifier : ' . $categorie->getNom(),
            'categorie' => $categorie,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_categorie_delete', methods: ['POST'])]
    public function delete(Categorie $categorie, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete-cat-' . $categorie->getId(), $request->request->get('_token'))) {
            $em->remove($categorie);
            $em->flush();
            $this->addFlash('success', 'Catégorie supprimée.');
        }
        return $this->redirectToRoute('admin_categorie_index');
    }
}
