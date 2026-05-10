<?php

namespace App\Controller\User;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user/cart')]
class CartController extends AbstractController
{
    #[Route('/', name: 'app_cart_index')]
    public function index(CartService $cartService): Response
    {
        return $this->render('user/cart/index.html.twig', [
            'items' => $cartService->getFullCart(),
            'total' => $cartService->getTotal()
        ]);
    }

    #[Route('/add/{id}', name: 'app_cart_add')]
    public function add(int $id, CartService $cartService, Request $request): Response
    {
        $cartService->add($id);
        $this->addFlash('success', 'Produit ajouté au panier avec succès !');

        // Rediriger vers la page d'où l'utilisateur vient, ou vers la boutique par défaut
        $referer = $request->headers->get('referer');
        return $referer ? $this->redirect($referer) : $this->redirectToRoute('app_user_vitrine');
    }

    #[Route('/remove/{id}', name: 'app_cart_remove')]
    public function remove(int $id, CartService $cartService): Response
    {
        $cartService->remove($id);
        $this->addFlash('success', 'Produit retiré du panier.');

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/decrement/{id}', name: 'app_cart_decrement')]
    public function decrement(int $id, CartService $cartService): Response
    {
        $cartService->decrement($id);
        
        return $this->redirectToRoute('app_cart_index');
    }
    #[Route('/checkout', name: 'app_user_commande_new')]
    public function checkout(): Response
    {
        // TODO: Implémenter la logique de création de commande
        $this->addFlash('success', 'Page de validation de commande à implémenter.');
        return $this->redirectToRoute('app_user_vitrine');
    }
}
