<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    public function getTotalRevenue(): float
    {
        return (float) $this->createQueryBuilder('c')
            ->select('SUM(l.quantite * l.prixUnitaire)')
            ->join('c.ligneCommandes', 'l')
            ->where('c.statut = :status')
            ->setParameter('status', 'Complétée')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }


    public function getMonthlyOrders(): array
    {
        return $this->createQueryBuilder('c')
            ->select('SUBSTRING(c.dateCreation, 1, 7) AS month, COUNT(c.id) AS count')
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne toutes les commandes d'un utilisateur, triées par date décroissante
     */
    public function findByUtilisateur($user): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.utilisateur = :user')
            ->setParameter('user', $user)
            ->orderBy('c.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
