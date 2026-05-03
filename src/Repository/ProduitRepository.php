<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function search($nom, $categorie)
    {
        $qb = $this->createQueryBuilder('p');

        if ($nom != null && $nom != '') {
            $qb->andWhere('p.nom LIKE :nom')
               ->setParameter('nom', '%'.$nom.'%');
        }

        if ($categorie != null && $categorie != '') {
            $qb->andWhere('p.categorie = :cat')
               ->setParameter('cat', $categorie);
        }

        return $qb->getQuery()->getResult();
    }
}
