<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }
    public function getTotalRevenue(): float
    {
        return $this->createQueryBuilder('c')
            ->select('SUM(lc.prixUnitaire * lc.quantite)')
            ->join('c.ligneCommandes', 'lc')
            ->getQuery()
            ->getSingleScalarResult() ?? 0.0;
    }

    public function getMonthlyOrders(): array
    {
        $date = new \DateTime();
        $date->modify('-6 months');

        return $this->createQueryBuilder('c')
            ->select('SUBSTRING(c.dateCreation, 1, 7) as month, COUNT(c.id) as count')
            ->where('c.dateCreation >= :date')
            ->setParameter('date', $date)
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
