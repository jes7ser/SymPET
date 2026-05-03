<?php

namespace App\Controller\User;

use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user/boutique')]
class VitrineController extends AbstractController
{
    #[Route('/', name: 'app_user_vitrine')]
    public function index(ProduitRepository $rep, CategorieRepository $catRep, Request $request, PaginatorInterface $paginator): Response
    {
        $nom = $request->query->get('nom');
        $categorie = $request->query->get('categorie');

        if ($nom != null || $categorie != null) {
            $produitsTrouves = $rep->search($nom, $categorie);
        } else {
            $produitsTrouves = $rep->findAll();
        }

        // Pagination simple (comme le prof)
        $produits = $paginator->paginate(
            $produitsTrouves, 
            $request->query->getInt('page', 1), 
            8 // 8 produits par page
        );

        $categories = $catRep->findAll();

        return $this->render('user/vitrine/index.html.twig', [
            'produits' => $produits,
            'categories' => $categories,
            'nom' => $nom,
            'categorieChoisie' => $categorie
        ]);
    }

    #[Route('/produit/{id}', name: 'app_user_vitrine_show')]
    public function show(ProduitRepository $rep, $id): Response
    {
        $produit = $rep->find($id);

        return $this->render('user/vitrine/show.html.twig', [
            'produit' => $produit,
        ]);
    }
}
