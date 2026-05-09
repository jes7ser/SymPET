<?php

namespace App\Controller\User;

use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use App\Service\ProduitFilterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user/boutique')]
class VitrineController extends AbstractController
{
    #[Route('/', name: 'app_user_vitrine')]
    public function index(
        Request $request,
        ProduitRepository $produitRepository,
        CategorieRepository $categorieRepository,
        ProduitFilterService $filterService
    ): Response {
        $produits = $filterService->getPage($request, 8);

        return $this->render('user/vitrine/index.html.twig', [
            'produits'    => $produits,
            'paginaMeta'  => $filterService->getPageMeta($produits),
            'categories'  => $categorieRepository->findAll(),
            'prixMinMax'  => $produitRepository->getPrixMinMax(),
            'filtres'     => $filterService->getActiveFilters($request),
        ]);
    }

    #[Route('/produit/{id}', name: 'app_user_vitrine_show')]
    public function show(ProduitRepository $produitRepository, int $id): Response
    {
        $produit = $produitRepository->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('Produit introuvable.');
        }

        return $this->render('user/vitrine/show.html.twig', [
            'produit' => $produit,
        ]);
    }
}
