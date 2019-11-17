<?php

namespace App\Repository;

use App\Entity\Saloon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

use App\Entity\User;

/**
 * @method Saloon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Saloon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Saloon[]    findAll()
 * @method Saloon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SaloonRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Saloon::class);
    }

    public function findOneBySlug($slug)
    {
        $qb = $this->createQueryBuilder('saloon')
            
            ->where(':slug = saloon.slug')
            ->setParameters(array('slug' => $slug))

            ->leftJoin('saloon.vacation', 'vacation')
            ->leftJoin('saloon.prices', 'prices')
            ->leftJoin('saloon.schedules', 'schedules')

            ->addSelect('vacation')
            ->addSelect('prices')
            ->addSelect('schedules')
        
            ->getQuery()
        ;

        return $qb->getOneOrNullResult();
    }

    public function findSaloonsByManager(User $manager)
    {
        $qb = $this->createQueryBuilder("user")
            
            ->where(':manager MEMBER OF user.managers')
            ->setParameters(array('manager' => $manager))
        
            ->getQuery()
        ;

        return $qb->getResult();
    }

    public function findClosest($latitude, $longitude)
    {
        
        $qb = $this->createQueryBuilder('saloon')
        
            ->addSelect('((ACOS(SIN(:latitude * PI() / 180) * SIN(saloon.latitude * PI() / 180) + COS(:latitude * PI() / 180) * COS(saloon.latitude * PI() / 180) * COS((:longitude - saloon.longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) as HIDDEN distance')
            
            ->setParameter('latitude', $latitude)
            ->setParameter('longitude', $longitude)

            ->leftJoin('saloon.vacation', 'vacation')
            ->addSelect('vacation')

            ->orderBy('distance')
        ;
        
        return $qb->getQuery()->getResult();
    }
}
