<?php

namespace App\Repository;

use App\Entity\SaleProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SaleProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method SaleProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method SaleProduct[]    findAll()
 * @method SaleProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SaleProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SaleProduct::class);
    }

    // /**
    //  * @return SaleProduct[] Returns an array of SaleProduct objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SaleProduct
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
