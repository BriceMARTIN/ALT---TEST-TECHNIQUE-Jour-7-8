<?php

namespace App\Repository;

use App\Entity\UsageLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UsageLog>
 *
 * @method UsageLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsageLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsageLog[]    findAll()
 * @method UsageLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class UsageLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsageLog::class);
    }

    public function save(UsageLog $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UsageLog $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
