<?php

namespace App\Repository;

use App\Entity\Log;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Log|null find($id, $lockMode = null, $lockVersion = null)
 * @method Log|null findOneBy(array $criteria, array $orderBy = null)
 * @method Log[]    findAll()
 * @method Log[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }

    /**
    * @return Log[] Returns an array of Log objects
    */

    public function findLastByRoute($route)
    {
        return $this->createQueryBuilder('l')
            ->andWhere(
                (new Expr)->eq('l.route', ':route')
            )
            ->setParameter('route', $route)
            ->orderBy('l.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
    * @return Log[] Returns an array of Log objects
    */

    public function findListFilterded(?array $filters = null): array
    {
        $qb = $this->createQueryBuilder('l');

        if (array_key_exists('createdAt', $filters) && null !== $filters['createdAt']) {
            $qb->andWhere($qb->expr()->between('l.createdAt', ':dateStart', ':dateEnd'));
            $createdAt = $filters['createdAt'];
            $createdAt->setTime(0,0,0);
            $qb->setParameter('dateStart' , $createdAt->format('Y-m-d  H:i:s'));
            $createdAt->setTime(23,59,59);
            $qb->setParameter('dateEnd' , $createdAt->format('Y-m-d  H:i:s'));
        }

        if (array_key_exists('route', $filters) && null !== $filters['route']) {
            $qb
                ->andWhere($qb->expr()->eq('l', ':route'));
            $qb->setParameter('route' , $filters['route']);
        }

        if (array_key_exists('status', $filters) && null !== $filters['status']) {
            $qb->andWhere($qb->expr()->eq('l.status', ':status'));
            $qb->setParameter('status' , $filters['status']);
        }

        return $qb
            ->orderBy('l.createdAt', 'DESC')
            ->addOrderBy('l.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}
