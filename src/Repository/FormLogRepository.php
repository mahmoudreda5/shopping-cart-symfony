<?php

namespace App\Repository;

use App\Entity\FormLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FormLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormLog[]    findAll()
 * @method FormLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormLog::class);
    }

    // /**
    //  * @return FormLog[] Returns an array of FormLog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FormLog
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

}
