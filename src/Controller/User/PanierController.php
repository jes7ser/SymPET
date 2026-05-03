<?php

namespace App\Controller\User;

use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/panier')]
class PanierController extends AbstractController
{
    #[Route('', name: 'panier_index')]
    public function index(PanierService $panier): Response
    {
        return $this->render('user/panier/index.html.twig', [
            'items' => $panier->getPanierComplet(),
            'total' => $panier->getTotal(),
        ]);
    }

    #[Route('/ajouter/{id}', name: 'panier_ajouter', requirements: ['id' => '\d+'])]
    public function ajouter(int $id, PanierService $panier): Response
    {
        $panier->ajouter($id);
        $this->addFlash('success', 'Produit ajouté au panier.');
        return $this->redirectToRoute('panier_index');
    }

    #[Route('/modifier/{id}', name: 'panier_modifier', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function modifier(int $id, Request $request, PanierService $panier): Response
    {
        $quantite = (int) $request->request->get('quantite', 1);
        $panier->modifier($id, $quantite);
        return $this->redirectToRoute('panier_index');
    }

    #[Route('/supprimer/{id}', name: 'panier_supprimer', requirements: ['id' => '\d+'])]
    public function supprimer(int $id, PanierService $panier): Response
    {
        $panier->supprimer($id);
        $this->addFlash('success', 'Produit retiré du panier.');
        return $this->redirectToRoute('panier_index');
    }

    #[Route('/vider', name: 'panier_vider')]
    public function vider(PanierService $panier): Response
    {
        $panier->vider();
        $this->addFlash('success', 'Panier vidé.');
        return $this->redirectToRoute('panier_index');
    }
}
