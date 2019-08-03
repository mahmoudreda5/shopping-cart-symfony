<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Cart;
use App\Entity\OrderCart;
use App\Repository\CartRepository;
use App\Repository\OrderCartRepository;
use App\Entity\Product;
use App\Entity\SaleProduct;
use App\Entity\Item;
use App\Entity\OrderItem;

/**
 * @Route("/", name="app_")
*/
class AppController extends AbstractController
{
    /**
     * @Route("/home/{id}", name="home")
     */
    public function index(SaleProduct $saleProduct, OrderCartRepository $cartRepository)
    {


        // $cart = $cartRepository->find(3);
        // dump($carts); die;

        /** @var User $user */
        $user = $this->getUser();

        $userCart = $cartRepository->findOrderCartByUser($user);
        // dump($userCart); die;

        $item = new OrderItem();
        $item->setQuantity(7);
        $item->setProduct($saleProduct);

        $userCart->addItem($item);

        // $cart = new OrderCart();
        // $cart->setItemsNumber(7);
        // $cart->setTotalPrice(170);

        // $user->addCart($cart);

        // $product = new SaleProduct();
        // $product->setName("product");
        // $product->setDescription("product des");
        // $product->setPrice(70);
        // $product->setQuantity(17);
        // $product->setImage("conan.jpg");

        // $product->setSalePrice(35);
        // $product->setDiscount(50);


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($item);
        // $entityManager->remove($cart);
        $entityManager->flush();

        dump($item); die;

        // return $this->render('app/index.html.twig', [
        //     'controller_name' => 'AppController',
        // ]);
    }
}
