<?php

namespace App\Repository;

use App\Entity\LigneCommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LigneCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LigneCommande::class);
    }
    public function getBestSeller(): ?array
    {
        return $this->createQueryBuilder('lc')
            ->select('p.nom as name, SUM(lc.quantite) as totalSales')
            ->join('lc.produit', 'p')
            ->groupBy('p.id')
            ->orderBy('totalSales', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
