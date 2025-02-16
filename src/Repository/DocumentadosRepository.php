<?php

namespace App\Repository;

use App\Entity\Documentados;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Documentados|null find($id, $lockMode = null, $lockVersion = null)
 * @method Documentados|null findOneBy(array $criteria, array $orderBy = null)
 * @method Documentados[]    findAll()
 * @method Documentados[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentadosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Documentados::class);
    }

    // /**
    //  * @return Documentados[] Returns an array of Documentados objects
    //  */

    public function findDocumentadosOrdered()
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.fechaNotificacion', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }


    // /**
    //  * @return Documentados[] Returns an array of Documentados objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Documentados
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
