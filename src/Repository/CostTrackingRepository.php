<?php

namespace App\Repository;

use App\Entity\CostTracking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CostTracking>
 *
 * @method CostTracking|null find($id, $lockMode = null, $lockVersion = null)
 * @method CostTracking|null findOneBy(array $criteria, array $orderBy = null)
 * @method CostTracking[]    findAll()
 * @method CostTracking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CostTrackingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CostTracking::class);
    }

    public function save(CostTracking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CostTracking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
