<?php

namespace App\Repository;

use App\Entity\Version;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Version|null find($id, $lockMode = null, $lockVersion = null)
 * @method Version|null findOneBy(array $criteria, array $orderBy = null)
 * @method Version[]    findAll()
 * @method Version[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Version::class);
    }

    /**
    * @return Version[] Returns an array of Version objects
    */

    public function findAllByTagDesc(): array
    {
        return $this->createQueryBuilder('v')
            ->orderBy('v.tag', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findLastVersion(): ?string
    {
        $versions = $this->createQueryBuilder('v')
            ->orderBy('v.tag', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;

        $version =  array_shift($versions);
        if ($version) {
            return $version->getTag();
        }
        
        return null;
    }

    public function findOneByTag(string $tag): ?Version
    {
        return $this->createQueryBuilder('v')
            ->where(
                (new Expr)->eq('v.tag', ':tag')
            )
            ->setParameter('tag', $tag)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
