<?php

namespace App\Repository;

use App\Entity\CartItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\ComponentInterface\CartItem\CartItemInterface;
use App\ComponentInterface\Repos\CartItemRepositoryInterface;

/**
 * @method Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method Item|null findOneBy(array $criteria, array $orderBy = null)
 * @method Item[]    findAll()
 * @method Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartItemRepository extends ServiceEntityRepository implements CartItemRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CartItem::class);
    }

    // /**
    //  * @return Item[] Returns an array of Item objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Item
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * {@inheritdoc}
     */
    public function findProductsWithCartId(int $cart_id){

        return $this->createQueryBuilder('c')
            ->innerJoin('c.product', 'p')
            ->innerJoin('c.cart', 'cart')
            ->addSelect('p')
            ->andWhere('cart.id = :id')
            ->setParameter('id', $cart_id)
            ->getQuery()
            ->getArrayResult()
        ;

    }

    /**
     * {@inheritdoc}
     */
    public function findCartItemByCartIdAndProductId(int $cart_id, int $product_id){

        return $this->createQueryBuilder('c')
            ->innerJoin('c.product', 'p')
            ->innerJoin('c.cart', 'cart')
            ->addSelect('c')
            ->andWhere('cart.id = :cart_id')
            ->andWhere('p.id = :product_id')
            ->setParameter('cart_id', $cart_id)
            ->setParameter('product_id', $product_id)
            ->getQuery()
            ->getOneOrNullResult()
        ;

    }
}
