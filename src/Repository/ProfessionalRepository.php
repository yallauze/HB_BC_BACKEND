<?php

namespace App\Repository;

use App\Entity\Professional;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Professional|null find($id, $lockMode = null, $lockVersion = null)
 * @method Professional|null findOneBy(array $criteria, array $orderBy = null)
 * @method Professional[]    findAll()
 * @method Professional[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfessionalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Professional::class);
    }
    // admin get all
   /* public function findProfessionals()
    {
        return $this->createQueryBuilder('pro')
            ->select('pro.id','pro.firstname', 'pro.lastname', 'pro.email', 'pro.tel', 'pro.siretNumber', 'user.username')
            ->leftJoin('pro.user', 'user')
            ->getQuery()
            ->getResult()
        ;
    } */
    public function findProfessionals()
    {

        return $this->createQueryBuilder('pro')
            ->select('pro.id','pro.firstname', 'pro.lastname', 'pro.email', 'pro.tel', 'pro.siretNumber', 'user.username', 'COUNT(g.id) as nb_garage', 'COUNT(a.id) as nb_advert')
            ->leftJoin('pro.user', 'user')
            ->leftJoin('pro.garages', 'g')
            ->leftJoin('g.adverts', 'a')
            ->groupBy('g.id')
            ->groupBy('pro.id')
            ->getQuery()
            ->getResult()
        ;
    } 
    // admin get one by id
    public function findProfessionalById(int $proId)
    {
        return $this->createQueryBuilder('pro')
            ->select('pro.id', 'pro.firstname', 'pro.lastname', 'pro.email', 'pro.tel', 'pro.siretNumber', 'user.username')
            ->leftJoin('pro.user', 'user')
            ->andWhere('pro.id = '.$proId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    // pro get himself's userId
    public function findProfessionalByUserId(int $userId)
    {
        return $this->createQueryBuilder('pro')
            ->select('pro.id','pro.firstname', 'pro.lastname', 'pro.email', 'pro.tel', 'pro.siretNumber', 'user.username')
            ->leftJoin('pro.user', 'user')
            ->andWhere('user.id = '.$userId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }


    // admin => stat
    public function findProfessionalsTotalNumber()
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id) as nb_professional')
            ->getQuery()
            ->getOneOrNullResult();
    }


    // /**
    //  * @return Professional[] Returns an array of Professional objects
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
    public function findOneBySomeField($value): ?Professional
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
