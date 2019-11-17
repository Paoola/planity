<?php

namespace App\Repository;

use App\Entity\Pause;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Pause|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pause|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pause[]    findAll()
 * @method Pause[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PauseRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Pause::class);
    }

//    /**
//     * @return Pause[] Returns an array of Pause objects
//     */
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
    public function findOneBySomeField($value): ?Pause
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
