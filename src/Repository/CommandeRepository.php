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

    public function getTotalCA(): float
    {
        $result = $this->createQueryBuilder('c')
            ->select('SUM(c.total)')
            ->where('c.statut = :statut')
            ->setParameter('statut', Commande::STATUT_COMPLETEE)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) $result;
    }

    public function getStatsMensuelles(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT YEAR(created_at) AS annee, MONTH(created_at) AS mois,
                       SUM(total) AS total, COUNT(id) AS nb
                FROM commande
                WHERE statut = :statut
                GROUP BY annee, mois
                ORDER BY annee ASC, mois ASC
                LIMIT 12";

        $result = $conn->executeQuery($sql, ['statut' => Commande::STATUT_COMPLETEE]);

        return $result->fetchAllAssociative();
    }
}
