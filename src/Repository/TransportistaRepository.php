<?php

namespace App\Repository;

use App\Entity\Transportista;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transportista|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transportista|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transportista[]    findAll()
 * @method Transportista[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransportistaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transportista::class);
    }

    // /**
    //  * @return Transportista[] Returns an array of Transportista objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Transportista
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
