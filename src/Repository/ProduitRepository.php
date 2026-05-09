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
    public function buildFilterQuery(string $nom, string $categorie, string $prixMin, string $prixMax, string $promo = '', string $produitType = '')
    {
        $qb = $this->createQueryBuilder('p');

        if ($nom != '') {
            $qb->andWhere('p.nom LIKE :nom')
               ->setParameter('nom', '%' . $nom . '%');
        }

        if ($categorie != '') {
            $qb->andWhere('p.categorie = :categorie')
               ->setParameter('categorie', $categorie);
        }

        if ($prixMin != '') {
            $qb->andWhere('p.prix >= :prixMin')
               ->setParameter('prixMin', $prixMin);
        }

        if ($prixMax != '') {
            $qb->andWhere('p.prix <= :prixMax')
               ->setParameter('prixMax', $prixMax);
        }

        if ($produitType != '') {
            $qb->andWhere('p.produitType = :produitType')
               ->setParameter('produitType', $produitType);
        }

        if ($promo == '1') {
            $qb->andWhere('p.prix < 30');
        }

        $qb->orderBy('p.id', 'DESC');

        return $qb;
    }

    public function getPrixMinMax(): array
    {
        $result = $this->createQueryBuilder('p')
            ->select('MIN(p.prix) as min, MAX(p.prix) as max')
            ->getQuery()
            ->getSingleResult();

        return [
            'min' => (int) floor($result['min'] ?? 0),
            'max' => (int) ceil($result['max'] ?? 0)
        ];
    }
}
