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

#[Route('/admin/user')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    #[Route('/', name: 'admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/{id}/toggle-status', name: 'admin_user_toggle_status', methods: ['POST'])]
    public function toggleStatus(User $user, EntityManagerInterface $entityManager): Response
    {
        // Empêcher de se désactiver soi-même
        if ($user === $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas désactiver votre propre compte.');
            return $this->redirectToRoute('admin_user_index');
        }

        $user->setIsEnabled(!$user->isEnabled());
        $entityManager->flush();

        $this->addFlash('success', 'Le statut de l\'utilisateur a été mis à jour.');

        return $this->redirectToRoute('admin_user_index');
    }

    #[Route('/{id}/change-role', name: 'admin_user_change_role', methods: ['POST'])]
    public function changeRole(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $newRole = $request->request->get('role');
        
        if (in_array($newRole, ['ROLE_USER', 'ROLE_ADMIN'])) {
            $user->setRoles([$newRole]);
            $entityManager->flush();
            $this->addFlash('success', 'Le rôle de l\'utilisateur a été modifié.');
        }

        return $this->redirectToRoute('admin_user_index');
    }

    #[Route('/{id}', name: 'admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            // Vérifier si l'utilisateur a des commandes
            if ($user->getCommandes()->count() > 0) {
                $this->addFlash('error', 'Impossible de supprimer cet utilisateur car il a passé des commandes. Veuillez plutôt le désactiver.');
            } else {
                $entityManager->remove($user);
                $entityManager->flush();
                $this->addFlash('success', 'L\'utilisateur a été supprimé.');
            }
        }

        return $this->redirectToRoute('admin_user_index');
    }
}
