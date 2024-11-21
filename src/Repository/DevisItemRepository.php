<?php

namespace App\Repository;

use App\Entity\DevisItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DevisItem>
 *
 * @method DevisItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method DevisItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method DevisItem[]    findAll()
 * @method DevisItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DevisItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DevisItem::class);
    }

//    /**
//     * @return DevisItem[] Returns an array of DevisItem objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DevisItem
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
