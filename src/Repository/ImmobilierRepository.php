<?php

namespace App\Repository;

use App\Entity\Immobilier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Immobilier>
 *
 * @method Immobilier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Immobilier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Immobilier[]    findAll()
 * @method Immobilier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImmobilierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Immobilier::class);
    }

//    /**
//     * @return Immobilier[] Returns an array of Immobilier objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

public function immobsListByUsers($userId)
{
    return $this->createQueryBuilder('i')
        ->andWhere('i.id_user = :userId')
        ->setParameter('userId', $userId)
        ->orderBy('i.id_fiscal', 'ASC')
        ->getQuery()
        ->getResult();
}
public function findBySearchTerm($searchTerm, $sortBy)
{
    $queryBuilder = $this->createQueryBuilder('i');

    // Filtrer par le terme de recherche
    if ($searchTerm) {
        $queryBuilder->andWhere('i.adresse LIKE :searchTerm')
                    ->setParameter('searchTerm', '%'.$searchTerm.'%');
    }

    // Trier les résultats
    $queryBuilder->orderBy('i.'.$sortBy, 'ASC');

    return $queryBuilder->getQuery()->getResult();
}
}
