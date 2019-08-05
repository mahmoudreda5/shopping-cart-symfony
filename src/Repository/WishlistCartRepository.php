<?php

namespace App\Repository;

use App\Entity\WishlistCart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\ComponentInterface\Repos\CartRepositoryInterface;
use App\Entity\User;
use App\ComponentInterface\Cart\CartInterface;

/**
 * @method WishlistCart|null find($id, $lockMode = null, $lockVersion = null)
 * @method WishlistCart|null findOneBy(array $criteria, array $orderBy = null)
 * @method WishlistCart[]    findAll()
 * @method WishlistCart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WishlistCartRepository extends ServiceEntityRepository implements CartRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WishlistCart::class);
    }

    // /**
    //  * @return WishlistCart[] Returns an array of WishlistCart objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WishlistCart
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * {@inheritdoc}
     */
    public function findCartByUser(User $user){

        return $this->createQueryBuilder('w')
            ->innerJoin('w.user', 'u')
            // ->addSelect('u.id AS user_id')
            ->andWhere('u.id = :user_id')
            ->setParameter('user_id', $user->getId())
            ->getQuery()
            ->getOneOrNullResult()
        ;

    }
}
