<?php

namespace App\Repository;

use App\Entity\Meuble;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MeubleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meuble::class);
    }

    public function search(string $q = '', string $categorieSlug = '', ?float $prixMin = null, ?float $prixMax = null): array
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.categorie', 'c')
            ->addSelect('c');

        if ($q) {
            $qb->andWhere('m.nom LIKE :q OR m.description LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }

        if ($categorieSlug) {
            $qb->andWhere('c.slug = :slug')
               ->setParameter('slug', $categorieSlug);
        }

        if ($prixMin !== null) {
            $qb->andWhere('m.prix >= :prixMin')
               ->setParameter('prixMin', $prixMin);
        }

        if ($prixMax !== null) {
            $qb->andWhere('m.prix <= :prixMax')
               ->setParameter('prixMax', $prixMax);
        }

        return $qb->orderBy('m.createdAt', 'DESC')->getQuery()->getResult();
    }
}
