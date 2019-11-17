<?php

namespace App\Repository;

use App\Entity\Slot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Slot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Slot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Slot[]    findAll()
 * @method Slot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlotRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Slot::class);
    }

    public function findBySaloonAndDate($saloon, $date)
    {
        $from = new \DateTime($date->format('Y-m-d') . ' 00:00:00');
        $to   = new \DateTime($date->format('Y-m-d') . ' 23:59:59');

        $qb = $this->createQueryBuilder('slot');

        $qb->select($qb->expr()->count('slot'))
            
            ->where('slot.start BETWEEN :from AND :to')
            ->setParameter('from', $from )
            ->setParameter('to', $to)

            ->andWhere('slot.saloon = :saloon')
            ->setParameter('saloon', $saloon)

            ->andWhere('slot.isValid = true')
        ;

        return $qb
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findByNextSaloon($saloon, $date)
    {
        $qb = $this->createQueryBuilder('slot')
            
            ->where('slot.start > :date')
            ->setParameter('date', $date)

            ->andWhere('slot.saloon = :saloon')
            ->setParameter('saloon', $saloon)

            ->andWhere('slot.isValid = true')

            ->orderBy('slot.start', 'ASC')
        ;

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByNextWorker($worker, $date)
    {
        $qb = $this->createQueryBuilder('slot')
            
            ->where('slot.start > :date')
            ->setParameter('date', $date)

            ->andWhere('slot.worker = :worker')
            ->setParameter('worker', $worker)

            ->orderBy('slot.start', 'ASC')
        ;

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }
}
