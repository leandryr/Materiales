<?php

namespace App\Repository;

use App\Entity\Planta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Planta|null find($id, $lockMode = null, $lockVersion = null)
 * @method Planta|null findOneBy(array $criteria, array $orderBy = null)
 * @method Planta[]    findAll()
 * @method Planta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlantaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Planta::class);
    }

    // /**
    //  * @return Planta[] Returns an array of Planta objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Planta
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
