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

    public function search(?string $query = null, ?int $categorieId = null): array
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.categorie', 'c')
            ->addSelect('c')
            ->where('m.stock > 0');

        if ($query) {
            $qb->andWhere('m.nom LIKE :q OR m.description LIKE :q')
               ->setParameter('q', '%' . $query . '%');
        }

        if ($categorieId) {
            $qb->andWhere('c.id = :cat')
               ->setParameter('cat', $categorieId);
        }

        return $qb->orderBy('m.createdAt', 'DESC')->getQuery()->getResult();
    }
}