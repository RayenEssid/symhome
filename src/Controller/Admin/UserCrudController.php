<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/utilisateurs')]
#[IsGranted('ROLE_ADMIN')]
class UserCrudController extends AbstractController
{
    #[Route('', name: 'admin_user_index')]
    public function index(UserRepository $repo): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'utilisateurs' => $repo->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/{id}/toggle-admin', name: 'admin_user_toggle', methods: ['POST'])]
    public function toggleAdmin(User $user, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('toggle-user-' . $user->getId(), $request->request->get('_token'))) {
            $roles = $user->getRoles();
            if (in_array('ROLE_ADMIN', $roles)) {
                $user->setRoles(['ROLE_USER']);
                $this->addFlash('success', $user->getNomComplet() . ' n\'est plus administrateur.');
            } else {
                $user->setRoles(['ROLE_ADMIN']);
                $this->addFlash('success', $user->getNomComplet() . ' est maintenant administrateur.');
            }
            $em->flush();
        }
        return $this->redirectToRoute('admin_user_index');
    }
}
