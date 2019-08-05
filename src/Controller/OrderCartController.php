<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\ComponentInterface\Factory\OrderCartFactory;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/order-cart", name="order_cart_")
 */
class OrderCartController extends AbstractController
{
    
    /**
     * @Route("/", name="index")
     */
    public function index(OrderCartFactory $orderCartFactory)
    {
        
        //get authenticated user orderCart products
        $items = $orderCartFactory->cartProducts();
        // dump($items); die;

        return $this->render('order_cart/index.html.twig', [
            'items' => $items,
            'totalPrice' => $orderCartFactory->getFactoryCart()->calculateTotalPrice()
        ]);
    }

    /**
     * @Route("/add-item/{id}", name="add_item")
     */
    public function addCartItem(Product $product, OrderCartFactory $orderCartFactory){

        //add product to authenticated user OrderCart
        $orderCartFactory->addProduct($product);

        return $this->redirectToRoute('app_show_product', ["id" => $product->getId()]);
    }

    /**
     * @Route("/remove-item/{id}", name="remove_item")
     */
    public function removeCartItem(Product $product, OrderCartFactory $orderCartFactory){

        /** CartFactory and product autowired for us now we can delete it using the cartFactory */
        $orderCartFactory->removeProduct($product);
        return $this->redirectToRoute('order_cart_index');

    }


     /**
     * @Route("/clear", name="clear")
     */
    public function clearCart(OrderCartFactory $orderCartFactory){

        $orderCartFactory->clearCart();
        return $this->redirectToRoute('order_cart_index');

    }

    /**
     * @Route("/edit-item/{id}", name="edit_item")
     */
    public function editCartItem(Product $product, OrderCartFactory $orderCartFactory, Request $request){

        //get quantity from request json content sent by javascript like: {quantity: 7}
        $quantity = json_decode($request->getContent());
        if($quantity){
            $orderCartFactory->editItemQuantity($product, $quantity->quantity);   
        }
        return new Response();
    }

}
