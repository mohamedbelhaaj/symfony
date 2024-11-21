<?php

namespace App\Repository;

use App\Entity\Vehicule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vehicule>
 *
 * @method Vehicule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vehicule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vehicule[]    findAll()
 * @method Vehicule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VehiculeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicule::class);
    }

//    /**
//     * @return Vehicule[] Returns an array of Vehicule objects
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

public function vehiclesListByUsers($userId)
{
    return $this->createQueryBuilder('v')
        ->andWhere('v.id_user = :userId')
        ->setParameter('userId', $userId)
        ->orderBy('v.matricule', 'ASC')
        ->getQuery()
        ->getResult();
}

public function findBySearchTerm($searchTerm, $sortBy)
{
    $queryBuilder = $this->createQueryBuilder('i');

    // Filtrer par le terme de recherche
    if ($searchTerm) {
        // Ajouter une condition pour rechercher par modèle ou par type
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('i.modele', ':searchTerm'),
                $queryBuilder->expr()->like('i.type', ':searchTerm')
            )
        )->setParameter('searchTerm', '%'.$searchTerm.'%');
    }

    // Trier les résultats
    $queryBuilder->orderBy('i.'.$sortBy, 'ASC');

    return $queryBuilder->getQuery()->getResult();
}
}
