<?php

namespace App\Repository;

use App\Entity\VerificationToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VerificationToken>
 *
 * @method VerificationToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method VerificationToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method VerificationToken[]    findAll()
 * @method VerificationToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VerificationTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VerificationToken::class);
    }

//    /**
//     * @return VerificationToken[] Returns an array of VerificationToken objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?VerificationToken
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
