<?php

namespace App\Service;

use App\Repository\ProduitRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Service de filtrage et de pagination des produits.
 * Délègue la pagination à PaginationService pour respecter
 * le principe de responsabilité unique (SRP).
 */
class ProduitFilterService
{
    public function __construct(
        private readonly ProduitRepository $produitRepository,
        private readonly PaginationService $paginationService
    ) {}

    /**
     * Retourne le QueryBuilder filtré selon les paramètres de la requête.
     */
    public function getFilteredQuery(Request $request): mixed
    {
        ['nom' => $nom, 'categorie' => $categorie, 'prix_min' => $prixMin, 'prix_max' => $prixMax, 'produit_type' => $produitType] =
            $this->getActiveFilters($request);

        $promo = $request->query->get('promo', '');

        return $this->produitRepository->buildFilterQuery($nom, $categorie, $prixMin, $prixMax, $promo, $produitType);
    }

    /**
     * Combine filtrage et pagination en un seul appel.
     * C'est la méthode principale à utiliser dans les contrôleurs.
     *
     * @param int $limit Nombre de produits par page
     * @return PaginationInterface
     */
    public function getPage(Request $request, int $limit = 8): PaginationInterface
    {
        $query = $this->getFilteredQuery($request);

        return $this->paginationService->paginate($query, $request, $limit);
    }

    /**
     * Retourne les métadonnées de pagination (début, fin, total, pages).
     * Pratique pour afficher "Produits 1-8 sur 50" dans le template.
     */
    public function getPageMeta(PaginationInterface $pagination): array
    {
        return $this->paginationService->getMeta($pagination);
    }

    /**
     * Retourne les filtres actifs sous forme de tableau simple.
     */
    public function getActiveFilters(Request $request): array
    {
        return [
            'nom'      => $request->query->get('nom', ''),
            'categorie'=> $request->query->get('categorie', ''),
            'prix_min' => $request->query->get('prix_min', ''),
            'prix_max' => $request->query->get('prix_max', ''),
            'produit_type' => $request->query->get('produit_type', ''),
        ];
    }
}
