<?php

namespace App\Repository;

use App\Entity\MediaFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MediaFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method MediaFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method MediaFile[]    findAll()
 * @method MediaFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MediaFile::class);
    }

    // /**
    //  * @return MediaFile[] Returns an array of MediaFile objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MediaFile
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
