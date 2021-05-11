<?php

namespace App\Repository;

use App\Entity\Channel;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\QueryException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Channel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Channel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Channel[]    findAll()
 * @method Channel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChannelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Channel::class);
    }

    /**
    * @return Program[] Returns an array of Program objects
    */
    public function findByTitle($query): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere(
                (new Expr)->like('c.title', ':query')
            )
            ->setParameter('query', '%'.$query.'%')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByTitle($query): ?Channel
    {
        try {
            return $this->createQueryBuilder('c')
                ->andWhere(
                    (new Expr)->like('c.title', ':query')
                )
                ->setParameter('query', $query)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        } catch (QueryException $e) {
            return null;
        }
    }

    public function findOneByIdDevice(int $idDevice): ?Channel
    {
        return $this->createQueryBuilder('c')
            ->andWhere(
                (new Expr)->eq('c.idDevice', ':idDevice')
            )
            ->setParameter('idDevice', $idDevice)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
