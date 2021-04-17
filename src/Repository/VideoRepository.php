<?php

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
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

    public function findListFilterded(?array $filters = null): array
    {
        $qb = $this->createQueryBuilder('v');

        if (array_key_exists('video', $filters) && null !== $filters['video']) {
            $qb->andWhere($qb->expr()->eq('v', ':video'));
            $qb->setParameter('video' , $filters['video']);
        }

        if (array_key_exists('program', $filters) && null !== $filters['program']) {
            $qb
                ->join('v.program', 'p')
                ->andWhere($qb->expr()->eq('p', ':program'));
            $qb->setParameter('program' , $filters['program']);
        }

        if (array_key_exists('channel', $filters) && null !== $filters['channel']) {
            $qb
                ->join('v.channel', 'c')
                ->andWhere($qb->expr()->eq('c', ':channel'));
            $qb->setParameter('channel' , $filters['channel']);
        }

        if (array_key_exists('status', $filters) && null !== $filters['status']) {
            $qb->andWhere($qb->expr()->eq('v.status', ':status'));
            $qb->setParameter('status' , $filters['status']);
        }

        return $qb
            ->orderBy('v.broadcastAt', 'DESC')
            ->addOrderBy('v.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findVideosToDownload(bool $scarlar=false): array
    {
        $qb = $this->createQueryBuilder('v');
        if ($scarlar){
            $qb->select(['v','p','c']);
        } 
               
        $qb->leftJoin('v.program', 'p')
        ->leftJoin ('v.channel', 'c')
        ->where(
            (new Expr)->eq('v.status', 0)
        )
        ;

        $videos = $qb->getQuery()->getResult();
        if ($scarlar) {
            $videosArray = [];
            if (null !== $videos) {
                foreach($videos as $video) {
                    $broatcastAt = $video->getBroadcastAt();
                    $videosArray[] = [
                        'id_website' => $video->getId(),
                        'title' => $video->getTitle(),
                        'program_id_website' => $video->getProgram()->getId(),
                        'program_id' => $video->getProgram()->getIdRaspberry(),
                        'program' => $video->getProgram()->getTitle(),
                        'broadcast_at' => $broatcastAt->format('YY-m-d'),
                        'channel_id_website'=> $video->getChannel()->getId(),
                        'channel_id'=> $video->getChannel()->getIdRaspberry(),
                        'channel' => $video->getChannel()->getTitle(),
                        'url' => $video->getUrl(),
                        'status' => 0,
                    ];
                }
            }
            return $videosArray;
        } else { 
            return $videos;
        }
        
    }

    public function findVideosWithError(): array
    {
        return $this->createQueryBuilder('v')
        ->leftJoin('v.program', 'p')
        ->leftJoin ('v.channel', 'c')
        ->where(
            (new Expr)->gt('v.status', 2)
        )
        ->getQuery()
        ->getResult();
    }

    /**
    * @return Program[] Returns an array of Program objects
    */
    public function findByTitle($query)
    {
        return $this->createQueryBuilder('v')
            ->andWhere(
                (new Expr)->like('v.title', ':query')
            )
            ->setParameter('query', '%'.$query.'%')
            ->orderBy('v.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
