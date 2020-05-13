<?php

namespace App\Repository;

use App\Entity\Advert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Advert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advert[]    findAll()
 * @method Advert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advert::class);
    }

    public function findAllAdvertsForMainview()
    {
        return $this->createQueryBuilder('a')
            ->select('a.id', 'a.title', 'a.description', 'a.year_started_at as year', 'a.km', 'a.price', 'a.created_at', 'f.id as fuel_id', 'm.id as model_id', 'b.id as brand_id', 'p.data_base64 as photo')
            ->leftJoin('a.fuel', 'f')
            ->leftJoin('a.model', 'm')
            ->leftJoin('m.brand', 'b')
            ->leftJoin('a.photos', 'p')
            ->distinct('a.id')
            ->orderBy('a.created_at')
            ->groupBy('a.id')
            ->getQuery()
            ->getResult();
    }

    // Admin/Pro => get one advert to edit
    public function findAdvertById(int $advertId)
    {
        return $this->createQueryBuilder('a')
            ->select('a.id', 'a.title', 'a.description', 'a.year_started_at', 'a.km', 'a.price', 'f.id as fuel_id', 'm.id as model_id', 'g.id as garage_id', 'b.id as brand_id')
            ->leftJoin('a.fuel', 'f')
            ->leftJoin('a.model', 'm')
            ->leftJoin('m.brand', 'b')
            ->leftJoin('a.garage', 'g')
            ->andWhere('a.id = ' . $advertId)
            ->getQuery()
            ->getOneOrNullResult();
    }
    // Admin => getAllAdverts
    public function findAllAdverts()
    {
        return $this->createQueryBuilder('a')
            ->select('a.id', 'a.title', 'a.price', 'a.created_at')
            ->getQuery()
            ->getResult();
    }

    // pro => getAllAdverts
    public function findAllAdvertsByProId(int $proId)
    {
        return $this->createQueryBuilder('a')
            ->select('a.id', 'a.title', 'a.price', 'a.created_at')
            ->leftJoin('a.garage', 'g')
            ->leftJoin('g.professional', 'p')
            ->andWhere('p.id = ' . $proId)
            ->getQuery()
            ->getResult();
    }


    // admin => stat
    public function findAdvertsTotalNumber()
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id) as nb_advert')
            ->getQuery()
            ->getOneOrNullResult();
    }

}
