<?php

namespace App\Repository;

use App\Entity\ArtworkComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArtworkComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArtworkComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArtworkComment[]    findAll()
 * @method ArtworkComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArtworkCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArtworkComment::class);
    }

    // /**
    //  * @return ArtworkComment[] Returns an array of ArtworkComment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ArtworkComment
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
