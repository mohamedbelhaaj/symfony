<?php

namespace App\Repository;

use App\Entity\Mechano;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mechano>
 *
 * @method Mechano|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mechano|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mechano[]    findAll()
 * @method Mechano[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MechanoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mechano::class);
    }

//    /**
//     * @return Mechano[] Returns an array of Mechano objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Mechano
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
