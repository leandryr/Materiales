<?php

namespace App\Repository;

use App\Entity\Registro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Registro|null find($id, $lockMode = null, $lockVersion = null)
 * @method Registro|null findOneBy(array $criteria, array $orderBy = null)
 * @method Registro[]    findAll()
 * @method Registro[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegistroRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Registro::class);
    }

    public function paginate($dql, $pagina = 1, $limit = 3)
    {
        $paginator = new Paginator($dql);
        $paginator->getQuery()
        ->setFirstResult($limit * ($pagina - 1)) // Offset
        ->setMaxResults($limit); // Limit

    return $paginator;
    }

    /**
        * @return Registro[]
        */
    public function getAll(
        $pagina =1,
        $limit = 10,
        $localidad = 'no',
        $planta = 'no',
        $tipo = 'no',
        $descripcion = 'no',
        $fechaEvento = 'no',
        $fechaEvento2 = 'no',
        $transportista = 'no',
        $fechaEmision = 'no',
        $fechaEmision2 = 'no',
        $fechaRespuesta = 'no',
        $fechaRespuesta2 = 'no',
        $fechaPago = 'no',
        $fechaPago2 = 'no',
        $estatus = 'no',
        $escalado = 'no',
        $ruta = 'no',
        $busqueda = 'no',
        $anoEvento = 'no',
        $anoAsignacion = 'no',
        $anoDocumentacion = 'no'
    ) {
        // Create our query
        $qb = $this->createQueryBuilder('p');

        if ($localidad !='no') {
            $qb
              ->andWhere('p.localidad = :localidad')
              ->setParameter('localidad', $localidad);
        }
        if ($planta!= 'no') {
            $qb
                ->andWhere('p.planta = :planta')
                ->setParameter('planta', $planta);
        }
        if ($tipo != 'no') {
            $qb
              ->andWhere('p.tipo = :tipo')
              ->setParameter('tipo', $tipo);
        }
        if ($descripcion != 'no') {
            $qb
              ->andWhere('p.descripcion = :descripcion')
              ->setParameter('descripcion', $descripcion);
        }
        if ($fechaEvento != 'no') {
            $qb
              ->andWhere('p.fechaEvento >= :fechaEvento')
              ->setParameter('fechaEvento', $fechaEvento);
        }
        if ($fechaEvento2 != 'no') {
            $qb
              ->andWhere('p.fechaEvento <= :fechaEvento2')
              ->setParameter('fechaEvento2', $fechaEvento2);
        }


        if ($transportista != 'no') {
            $qb
                ->andWhere('p.transportista = :transportista')
                ->setParameter('transportista', $transportista);
        }
        if ($fechaEmision != 'no') {
            $qb
              ->andWhere('p.fechaEmision >= :fechaEmision')
              ->setParameter('fechaEmision', $fechaEmision);
        }
        if ($fechaEmision2 != 'no') {
            $qb
              ->andWhere('p.fechaEmision <= :fechaEmision2')
              ->setParameter('fechaEmision2', $fechaEmision2);
        }

        if ($fechaRespuesta != 'no') {
            $qb
              ->andWhere('p.fechaRespuesta >= :fechaRespuesta')
              ->setParameter('fechaRespuesta', $fechaRespuesta);
        }
        if ($fechaRespuesta2 != 'no') {
            $qb
              ->andWhere('p.fechaRespuesta <= :fechaRespuesta2')
              ->setParameter('fechaRespuesta2', $fechaRespuesta2);
        }
        if ($fechaPago != 'no') {
            $qb
              ->andWhere('p.fechaAviso >= :fechaPago')
              ->setParameter('fechaPago', $fechaPago);
        }
        if ($fechaPago2 != 'no') {
            $qb
              ->andWhere('p.fechaAviso <= :fechaPago2')
              ->setParameter('fechaPago2', $fechaPago2);
        }
        if ($estatus !='no') {
            $qb
              ->andWhere('p.estatus = :estatus')
              ->setParameter('estatus', $estatus);
        }
        if ($escalado != 'no') {
            $qb
              ->andWhere('p.escalado = :escalado')
              ->setParameter('escalado', $escalado);
        }
        if ($ruta != 'no') {
            $qb
              ->andWhere('p.ruta = :ruta')
              ->setParameter('ruta', $ruta);
        }

        if ($busqueda != 'no') {
            $qb
              ->andWhere('p.referencia = :busqueda')
              ->setParameter('busqueda', $busqueda);
        }

        if ($anoEvento != 'no') {
            $qb
              ->andWhere('p.anoEvento = :anoEvento')
              ->setParameter('anoEvento', $anoEvento);
        }

        if ($anoAsignacion != 'no') {
            $qb
              ->andWhere('p.anoAsignacion = :anoAsignacion')
              ->setParameter('anoAsignacion', $anoAsignacion);
        }

        if ($anoDocumentacion != 'no') {
            $qb
              ->andWhere('p.anoDocumentacion = :anoDocumentacion')
              ->setParameter('anoDocumentacion', $anoDocumentacion);
        }



        $query = $qb
                    ->orderBy('p.estatus', 'ASC')
                    ->orderBy('p.fechaEvento', 'DESC')

                    ->getQuery();

        $paginator = $this->paginate($query, $pagina, $limit);
        return array('paginator' => $paginator, 'query' => $query);
    }


    /**
        * @return Registro[]
        */
    public function getAllNP(
        $localidad = 'no',
        $planta = 'no',
        $tipo = 'no',
        $descripcion = 'no',
        $fechaEvento = 'no',
        $fechaEvento2 = 'no',
        $transportista = 'no',
        $fechaEmision = 'no',
        $fechaEmision2 = 'no',
        $fechaRespuesta = 'no',
        $fechaRespuesta2 = 'no',
        $fechaPago = 'no',
        $fechaPago2 = 'no',
        $estatus = 'no',
        $escalado = 'no',
        $ruta = 'no',
        $busqueda = 'no',
        $anoEvento = 'no',
        $anoAsignacion = 'no',
        $anoDocumentacion = 'no'

    ) {
        // Create our query
        $qb = $this->createQueryBuilder('p');

        if ($localidad !='no') {
            $qb
              ->andWhere('p.localidad = :localidad')
              ->setParameter('localidad', $localidad);
        }
        if ($planta!= 'no') {
            $qb
                ->andWhere('p.planta = :planta')
                ->setParameter('planta', $planta);
        }
        if ($tipo != 'no') {
            $qb
              ->andWhere('p.tipo = :tipo')
              ->setParameter('tipo', $tipo);
        }
        if ($descripcion != 'no') {
            $qb
              ->andWhere('p.descripcion = :descripcion')
              ->setParameter('descripcion', $descripcion);
        }
        if ($fechaEvento != 'no') {
            $qb
              ->andWhere('p.fechaEvento >= :fechaEvento')
              ->setParameter('fechaEvento', $fechaEvento);
        }
        if ($fechaEvento2 != 'no') {
            $qb
              ->andWhere('p.fechaEvento <= :fechaEvento2')
              ->setParameter('fechaEvento2', $fechaEvento2);
        }


        if ($transportista !='no') {
            $qb
                ->andWhere('p.transportista = :transportista')
                ->setParameter('transportista', $transportista);
        }
        if ($fechaEmision != 'no') {
            $qb
              ->andWhere('p.fechaEmision >= :fechaEmision')
              ->setParameter('fechaEmision', $fechaEmision);
        }
        if ($fechaEmision2 != 'no') {
            $qb
              ->andWhere('p.fechaEmision <= :fechaEmision2')
              ->setParameter('fechaEmision2', $fechaEmision2);
        }

        if ($fechaRespuesta != 'no') {
            $qb
              ->andWhere('p.fechaRespuesta >= :fechaRespuesta')
              ->setParameter('fechaRespuesta', $fechaRespuesta);
        }
        if ($fechaRespuesta2 != 'no') {
            $qb
              ->andWhere('p.fechaRespuesta <= :fechaRespuesta2')
              ->setParameter('fechaRespuesta2', $fechaRespuesta2);
        }
        if ($fechaPago != 'no') {
            $qb
              ->andWhere('p.fechaAviso >= :fechaPago')
              ->setParameter('fechaPago', $fechaPago);
        }
        if ($fechaPago2 != 'no') {
            $qb
              ->andWhere('p.fechaAviso <= :fechaPago2')
              ->setParameter('fechaPago2', $fechaPago2);
        }
        if ($estatus !='no') {
            $qb
              ->andWhere('p.estatus = :estatus')
              ->setParameter('estatus', $estatus);
        }
        if ($escalado != 'no') {
            $qb
              ->andWhere('p.escalado = :escalado')
              ->setParameter('escalado', $escalado);
        }
        if ($ruta != 'no') {
            $qb
              ->andWhere('p.ruta = :ruta')
              ->setParameter('ruta', $ruta);
        }

        if ($busqueda != 'no') {
            $qb
              ->andWhere('p.referencia = :busqueda')
              ->setParameter('busqueda', $busqueda);
        }

        if ($anoEvento != 'no') {
            $qb
              ->andWhere('p.anoEvento = :anoEvento')
              ->setParameter('anoEvento', $anoEvento);
        }

        if ($anoAsignacion != 'no') {
            $qb
              ->andWhere('p.anoAsignacion = :anoAsignacion')
              ->setParameter('anoAsignacion', $anoAsignacion);
        }

        if ($anoDocumentacion != 'no') {
            $qb
              ->andWhere('p.anoDocumentacion = :anoDocumentacion')
              ->setParameter('anoDocumentacion', $anoDocumentacion);
        }




        $query = $qb
                    ->orderBy('p.estatus', 'ASC')
                    ->orderBy('p.fechaEvento', 'DESC')

                    ->getQuery();

        return $query->execute();
    }

    public function findTipos()
    {
        return $this->createQueryBuilder('r')
          ->select('r.tipo')
          ->groupBy('r.tipo')
          ->orderBy('r.tipo')
          ->getQuery()
          ->getResult()
      ;
    }

    public function findDescripciones()
    {
        return $this->createQueryBuilder('r')
        ->select('r.descripcion')
        ->groupBy('r.descripcion')
        ->orderBy('r.descripcion')
        ->getQuery()
        ->getResult()
    ;
    }

    public function findAllOrdered($inicio, $fin, $estatus)
    {
        // Create our query
        $qb = $this->createQueryBuilder('p');

        if ($estatus !='no') {
            $qb
            ->andWhere('p.estatus = :estatus')
            ->setParameter('estatus', $estatus);
        }

        if ($inicio !='no') {
            $qb
            ->andWhere('p.fechaEmision >= :fechaEmision')
            ->setParameter('fechaEmision', $inicio);
        }

        if ($fin !='no') {
            $qb
            ->andWhere('p.fechaEmision <= :fechaEmision')
            ->setParameter('fechaEmision', $fin);
        }


        $query = $qb
          ->orderBy('p.estatus', 'ASC')
          ->orderBy('p.fechaEmision', 'ASC')

          ->getQuery();

        return $query->execute();
    }

    public function findSemanales($inicio, $fin, $estatus)
    {
        // Create our query
        $qb = $this->createQueryBuilder('p');

        if ($estatus !='no') {
            $qb
            ->andWhere('p.estatus = :estatus')
            ->setParameter('estatus', $estatus);
        }


        $query = $qb
        ->andWhere('p.fechaEmision >= :inicio')
        ->setParameter('inicio', $inicio)
        ->andWhere('p.fechaEmision <= :fin')
        ->setParameter('fin', $fin)
        ->orderBy('p.estatus', 'ASC')
        ->orderBy('p.fechaEmision', 'DESC')

          ->getQuery();

        return $query->execute();
    }


    public function findAllOrdered2( $estatus)
    {
        // Create our query
        $qb = $this->createQueryBuilder('p');

        if ($estatus !='no') {
            $qb
            ->andWhere('p.estatus = :estatus')
            ->setParameter('estatus', $estatus);
        }

        $query = $qb
          ->orderBy('p.estatus', 'ASC')
          ->orderBy('p.fechaEmision', 'ASC')

          ->getQuery();

        return $query->execute();
    }

    public function findSemanales2($estatus)
    {
        // Create our query
        $qb = $this->createQueryBuilder('p');

        if ($estatus !='no') {
            $qb
            ->andWhere('p.estatus = :estatus3')
            ->setParameter('estatus3', $estatus);
        }else{
          $qb
          ->andWhere('p.estatus != :estatus')
          ->setParameter('estatus', 'Pagado')
          ->andWhere('p.estatus != :estatus2')
          ->setParameter('estatus2', 'Cancelado');
        }
        $query = $qb
        ->orderBy('p.estatus', 'ASC')
        ->orderBy('p.fechaEmision', 'DESC')
        ->getQuery();

        return $query->execute();
    }



    // /**
    //  * @return Registro[] Returns an array of Registro objects
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

    /*
    public function findOneBySomeField($value): ?Registro
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
