<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\OrderCartRepository;
use App\ComponentInterface\Factory\OrderCartFactory;

/**
 * @Route("/", name="app_")
*/
class AppController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(ProductRepository $productRepository)
    {

        $products = $productRepository->findAll();
        // dump($products); die;

        return $this->render("app/index.html.twig", ["products" => $products]);

    }

    /**
     * @Route("/show/product/{id}", name="show_product")
     */
    public function showProduct(Product $product, OrderCartFactory $orderCartFactory){

        //get authenticated user
        // /** @var User $user */
        // $user = $this->getUser();

        //check if product exist on authenticated user OrderCart
        $productAddedToCart = $orderCartFactory->hasProduct($product);

        return $this->render("app/product_show.html.twig", ["product" => $product, "productAddedToCart" => $productAddedToCart]);
        
    }

    /**
     * @Route("/order/product/{id}", name="order_product")
     */
    public function addProductToOrderCart(Product $product, OrderCartFactory $orderCartFactory){

        //add product to authenticated user OrderCart
        $orderCartFactory->addProduct($product);

        return $this->redirectToRoute('app_show_product', ["id" => $product->getId()]);
    }

}
