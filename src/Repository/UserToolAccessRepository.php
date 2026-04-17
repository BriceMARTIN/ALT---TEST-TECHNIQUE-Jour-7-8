<?php

namespace App\Repository;

use App\Entity\UserToolAccess;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserToolAccess>
 *
 * @method UserToolAccess|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserToolAccess|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserToolAccess[]    findAll()
 * @method UserToolAccess[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class UserToolAccessRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserToolAccess::class);
    }

    public function save(UserToolAccess $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserToolAccess $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
