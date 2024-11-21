<?php

namespace App\Repository;

use App\Entity\RDV;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RDV>
 *
 * @method RDV|null find($id, $lockMode = null, $lockVersion = null)
 * @method RDV|null findOneBy(array $criteria, array $orderBy = null)
 * @method RDV[]    findAll()
 * @method RDV[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RDVRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RDV::class);
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.date', 'ASC') // Trie par date croissante
            ->getQuery()
            ->getResult();
    }

}
