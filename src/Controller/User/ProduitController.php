<?php

namespace App\Controller\User;

use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProduitController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[Route('/produits', name: 'produit_index')]
    public function index(ProduitRepository $repo, CategorieRepository $catRepo): Response
    {
        return $this->render('user/produit/index.html.twig', [
            'produits'   => $repo->findAll(),
            'categories' => $catRepo->findAll(),
        ]);
    }

    #[Route('/produits/{id}', name: 'produit_show', requirements: ['id' => '\d+'])]
    public function show(int $id, ProduitRepository $repo): Response
    {
        $produit = $repo->find($id);
        if (!$produit) {
            throw $this->createNotFoundException('Produit introuvable.');
        }

        return $this->render('user/produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }
}
