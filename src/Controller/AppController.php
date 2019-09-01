<?php

namespace App\Controller;

use App\ComponentInterface\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\ComponentInterface\Factory\OrderCartFactory;
use App\ComponentInterface\Factory\WishlistCartFactory;

/**
 * @Route("/", name="app_")
*/
class AppController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(ProductService $productService)
    {

        $products = $productService->retrieveAllProducts();
        // dump($products); die;

        return $this->render("app/index.html.twig", ["products" => $products]);

    }

    /**
     * @Route("/show/product/{id}", name="show_product")
     */
    public function showProduct(Product $product, OrderCartFactory $orderCartFactory, WishlistCartFactory $wishlistCartFactory){

        //get authenticated user
        /** @var User $user */
        $user = $this->getUser();

        $productAddedToOrderCart = null;
        $productAddedToWishlistCart = null;
        if($user){
            //check if product exist on authenticated user OrderCart
            $productAddedToOrderCart = $orderCartFactory->hasProduct($product);

            //check if product exist on authenticated user wishlistCart
            $productAddedToWishlistCart = $wishlistCartFactory->hasProduct($product);
        }
        

        return $this->render("app/product_show.html.twig", [
            "product" => $product,
             "productAddedToOrderCart" => $productAddedToOrderCart,
             "productAddedToWishlistCart" => $productAddedToWishlistCart
        ]);
        
    }

}
