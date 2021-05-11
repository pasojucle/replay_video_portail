<?php

namespace App\Repository;

use App\Entity\Program;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\QueryException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Program|null find($id, $lockMode = null, $lockVersion = null)
 * @method Program|null findOneBy(array $criteria, array $orderBy = null)
 * @method Program[]    findAll()
 * @method Program[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgramRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Program::class);
    }

    /**
    * @return Program[] Returns an array of Program objects
    */
    public function findByTitle($query): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere(
                (new Expr)->like('p.title', ':query')
            )
            ->setParameter('query', '%'.$query.'%')
            ->orderBy('p.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByTitle($query): ?Program
    {
        try {
            return $this->createQueryBuilder('p')
                ->andWhere(
                    (new Expr)->like('p.title', ':query')
                )
                ->setParameter('query', $query)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        } catch (QueryException $e) {
            return null;
        }
    }

    public function findOneByIdDevice(int $idDevice): ?Program
    {
        return $this->createQueryBuilder('p')
            ->andWhere(
                (new Expr)->eq('p.idDevice', ':idDevice')
            )
            ->setParameter('idDevice', $idDevice)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
