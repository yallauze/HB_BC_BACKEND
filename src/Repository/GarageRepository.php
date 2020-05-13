<?php

namespace App\Repository;

use App\Entity\Garage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Garage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Garage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Garage[]    findAll()
 * @method Garage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GarageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Garage::class);
    }

    // all garages for admin (all professionals)
    public function findAllGarages()
    {
        return $this->createQueryBuilder('g')
            ->select('g.id', 'g.name', 'g.tel', 'g.city', 'pro.firstname as proFirstname', 'pro.lastname as proLastname')
            ->leftJoin('g.professional', 'pro')
            ->getQuery()
            ->getResult();
    }
    // all garages for admin/pro (by pro)
    public function findAllGaragesByProId(int $proId)
    {
        return $this->createQueryBuilder('g')
            ->select('g.id', 'g.name', 'g.tel', 'g.city')
            ->leftJoin('g.professional', 'pro')
            ->andWhere('pro.id = ' . $proId)
            ->getQuery()
            ->getResult();
    }
    // one garage for admin/pro (by garage)
    public function findOneGarageByGarageId(int $garageId)
    {
        return $this->createQueryBuilder('g')
            ->select('g.id', 'g.name', 'g.tel', 'g.street','g.city', 'g.postcode', 'g.country')
            ->andWhere('g.id = ' . $garageId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // admin => stat
    public function findGaragesTotalNumber()
    {
        return $this->createQueryBuilder('g')
            ->select('COUNT(g.id) as nb_garage')
            ->getQuery()
            ->getOneOrNullResult();
    }


    // /**
    //  * @return Garage[] Returns an array of Garage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Garage
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
