<?php

namespace App\Service;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Service centralisé de pagination.
 * Encapsule KnpPaginatorInterface pour éviter la répétition
 * de la logique page/limit/tri dans chaque contrôleur.
 */
class PaginationService
{
    public function __construct(
        private readonly PaginatorInterface $paginator
    ) {}

    /**
     * Pagine n'importe quel QueryBuilder, tableau ou requête Doctrine.
     *
     * @param mixed  $target      QueryBuilder ou itérable à paginer
     * @param Request $request    Requête HTTP courante (lecture du paramètre "page")
     * @param int    $limit       Nombre d'éléments par page (défaut : 8)
     * @param string $pageParam   Nom du paramètre GET de page (défaut : "page")
     * @param array  $options     Options supplémentaires à passer au paginateur
     */
    public function paginate(
        mixed $target,
        Request $request,
        int $limit = 8,
        string $pageParam = 'page',
        array $options = []
    ): PaginationInterface {
        $page = max(1, $request->query->getInt($pageParam, 1));

        $defaultOptions = [
            'pageParameterName'          => $pageParam,
            'sortFieldParameterName'     => 'sort',
            'sortDirectionParameterName' => 'direction',
            'defaultSortFieldName'       => 'p.id',
            'defaultSortDirection'       => 'desc',
        ];

        return $this->paginator->paginate(
            $target,
            $page,
            $limit,
            array_merge($defaultOptions, $options)
        );
    }

    /**
     * Retourne des métadonnées lisibles pour affichage dans les templates.
     * Ex : ['debut' => 1, 'fin' => 8, 'total' => 50, 'pages' => 7]
     */
    public function getMeta(PaginationInterface $pagination): array
    {
        $total   = $pagination->getTotalItemCount();
        $limit   = $pagination->getItemNumberPerPage();
        $current = $pagination->getCurrentPageNumber();

        $debut = ($current - 1) * $limit + 1;
        $fin   = min($current * $limit, $total);

        return [
            'debut'  => $total > 0 ? $debut : 0,
            'fin'    => $fin,
            'total'  => $total,
            'pages'  => (int) ceil($total / max(1, $limit)),
            'page'   => $current,
        ];
    }
}
