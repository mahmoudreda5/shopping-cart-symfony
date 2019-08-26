<?php

namespace App\Repository;

use App\Entity\OrderCartItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\ComponentInterface\Repos\CartItemRepositoryInterface;
use App\ComponentInterface\CartItem\CartItemInterface;
use App\ComponentInterface\Product\ProductInterface;

/**
 * @method OrderItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderItem[]    findAll()
 * @method OrderItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderCartItemRepository extends ServiceEntityRepository implements CartItemRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OrderCartItem::class);
    }

    // /**
    //  * @return OrderItem[] Returns an array of OrderItem objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrderItem
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
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

        return $this->createQueryBuilder('o')
            ->innerJoin('o.product', 'p')
            ->innerJoin('o.cart', 'cart')
            ->addSelect('p')
            ->andWhere('cart.id = :id')
            ->setParameter('id', $cart_id)
            ->getQuery()
            ->getArrayResult()
        ;

        // $sub =  $this->createQueryBuilder('any')
        //     ->select('c')
        //     ->from("App\Entity\CartItem", 'c')
        //     // ->innerJoin('c.cart', 'cart')
        //     ->where('c.cart_id = :cart_id')
        //     ->setParameter('cart_id', $cart_id)
        //     ->getQuery()
        //     ->getResult()
        // ;

        // return $this->createQueryBuilder('c')
        //     // ->innerJoin('c.product', 'p')
        //     // ->innerJoin('c.cart', 'cart')
        //     ->select('p')
        //     ->from('App\Entity\Product', 'p')
        //     ->where('p.id = :id')
        //     ->setParameter('id', 7)
        //     // ->andWhere('cart.id = :id')
        //     // ->setParameter('id', $cart_id)
        //     ->getQuery()
        //     ->getResult()
        // ;

    }

    /**
     * {@inheritdoc}
     */
    public function findCartItemByCartIdAndProductId(int $cart_id, int $product_id){

        return $this->createQueryBuilder('o')
            ->innerJoin('o.product', 'p')
            ->innerJoin('o.cart', 'cart')
            ->addSelect('o')
            ->andWhere('cart.id = :cart_id')
            ->andWhere('p.id = :product_id')
            ->setParameter('cart_id', $cart_id)
            ->setParameter('product_id', $product_id)
            ->getQuery()
            ->getOneOrNullResult()
        ;

    }

    
}
