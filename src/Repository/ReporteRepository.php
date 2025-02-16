<?php

namespace App\Repository;

use App\Entity\Reporte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reporte|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reporte|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reporte[]    findAll()
 * @method Reporte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReporteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reporte::class);
    }

    // /**
    //  * @return Reporte[] Returns an array of Reporte objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


    public function findPrimero($fechaEvento1, $fechaEvento2, $estatus, $tipoReporte)
    {
        // Create our query
        $qb = $this->createQueryBuilder('p');

        if ($estatus !='no') {
            $qb
            ->andWhere('p.estatus = :estatus')
            ->setParameter('estatus', $estatus);
        }
        if ($tipoReporte!= 'no') {
            if ($tipoReporte == 'menores') {
                $qb
              ->andWhere('p.mayor45 = 0');
            } else {
                $qb
              ->andWhere('p.mayor45 = 1');
            }
        }

        if ($fechaEvento1 != 'no') {
            $qb
            ->andWhere('p.fechaEvento >= :fechaEvento')
            ->setParameter('fechaEvento', $fechaEvento1);
        }
        if ($fechaEvento2 != 'no') {
            $qb
            ->andWhere('p.fechaEvento <= :fechaEvento2')
            ->setParameter('fechaEvento2', $fechaEvento2);
        }

        $query = $qb
                  ->andWhere('p.documentado = 0')
                  ->orderBy('p.estatus', 'ASC')
                  ->orderBy('p.fechaEvento', 'ASC')

                  ->getQuery();

        return $query->execute();
    }



    public function findPrimeroDocumentado()
    {
        return $this->createQueryBuilder('r')
          ->andWhere('r.fechaEvento is not NULL')
            ->andWhere('r.documentado = 1')
            ->orderBy('r.fechaEvento', 'ASC')
            ->getQuery()
            //->getOneOrNullResult()
            ->getResult()
        ;
    }

    public function findSemanalesMenores($inicio, $fin, $estatus)
    {
        // Create our query
        $qb = $this->createQueryBuilder('p');

        if ($estatus !='no') {
            $qb
            ->andWhere('p.estatus = :estatus')
            ->setParameter('estatus', $estatus);
        }

        $qb->andWhere('p.mayor45 = 0');

        $query = $qb
        ->andWhere('p.documentado = 0')
        ->andWhere('p.fechaEvento >= :inicio')
        ->setParameter('inicio', $inicio)
        ->andWhere('p.fechaEvento <= :fin')
        ->setParameter('fin', $fin)
          ->orderBy('p.estatus', 'ASC')
          ->orderBy('p.fechaEvento', 'DESC')

          ->getQuery();

        return $query->execute();
    }
    public function findSemanalesMayores($inicio, $fin, $estatus)
    {
        // Create our query
        $qb = $this->createQueryBuilder('p');

        if ($estatus !='no') {
            $qb
            ->andWhere('p.estatus = :estatus')
            ->setParameter('estatus', $estatus);
        }

        $qb->andWhere('p.mayor45 = 1');

        $query = $qb
        ->andWhere('p.documentado = 0')
        ->andWhere('p.fechaEvento >= :inicio')
        ->setParameter('inicio', $inicio)
        ->andWhere('p.fechaEvento <= :fin')
        ->setParameter('fin', $fin)
          ->orderBy('p.estatus', 'ASC')
          ->orderBy('p.fechaEvento', 'DESC')

          ->getQuery();

        return $query->execute();
    }


    public function findSemanalesDocumentados($inicio, $fin)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.documentado = 1')
            ->andWhere('r.fechaEvento >= :inicio')
            ->setParameter('inicio', $inicio)
            ->andWhere('r.fechaEvento <= :fin')
            ->setParameter('fin', $fin)

            ->orderBy('r.fechaEvento', 'DESC')
            ->getQuery()
            //->getOneOrNullResult()
            ->getResult()
        ;
    }
}
