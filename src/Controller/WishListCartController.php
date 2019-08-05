<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\ComponentInterface\Factory\WishlistCartFactory;
use App\Entity\Product;

/**
 * @Route("/wish-list", name="wishlist_cart_")
 */
class WishListCartController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(WishlistCartFactory $wishlistCartFactory)
    {

        $wishlistItems = $wishlistCartFactory->cartProducts();
        // dump($wishlistItems); die;

        return $this->render('wishlist_cart/index.html.twig', [
            'wishlistItems' => $wishlistItems
        ]);
    }

    /**
     * @Route("/add-item/{id}", name="add_item")
     */
    public function addCartItem(Product $product, WishlistCartFactory $wishlistCartFactory){

        //add product to authenticated user wishlistCart
        $wishlistCartFactory->addProduct($product);

        return $this->redirectToRoute('app_show_product', ["id" => $product->getId()]);
    }

    /**
     * @Route("/remove-item/{id}", name="remove_item")
     */
    public function removeCartItem(Product $product, WishlistCartFactory $wishlistCartFactory){

        /** CartFactory and product autowired for us now we can delete it using the cartFactory */
        $wishlistCartFactory->removeProduct($product);
        return $this->redirectToRoute('wishlist_cart_index');

    }


     /**
     * @Route("/clear", name="clear")
     */
    public function clearCart(WishlistCartFactory $wishlistCartFactory){

        $wishlistCartFactory->clearCart();
        return $this->redirectToRoute('wishlist_cart_index');

    }
}
