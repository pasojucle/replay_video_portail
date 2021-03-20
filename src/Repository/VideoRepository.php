<?php

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Video|null find($id, $lockMode = null, $lockVersion = null)
 * @method Video|null findOneBy(array $criteria, array $orderBy = null)
 * @method Video[]    findAll()
 * @method Video[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoRepository extends ServiceEntityRepository
{
    const MAX_RESULT = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class);
    }

    /**
    * @return Video[] Returns an array of Video objects
    */

    public function findList($page = 1): Paginator
    {
        $qb = $this->createQueryBuilder('v')
            ->orderBy('v.broadcastAt', 'DESC')
            ->addOrderBy('v.id', 'DESC')
            ->setFirstResult(($page -1) * self::MAX_RESULT +1)
            ->setMaxResults(self::MAX_RESULT)
        ;

        return new Paginator($qb);
    }
}
