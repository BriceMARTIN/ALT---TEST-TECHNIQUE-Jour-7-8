<?php

namespace App\Repository;

use App\Entity\AccessRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccessRequest>
 *
 * @method AccessRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccessRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccessRequest[]    findAll()
 * @method AccessRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class AccessRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessRequest::class);
    }

    public function save(AccessRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AccessRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
