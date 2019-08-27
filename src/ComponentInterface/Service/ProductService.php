<?php 

namespace App\ComponentInterface\Service;
use App\Repository\ProductRepository;



class ProductService implements ProductServiceInterface{

    private $productRepo;
    public function __construct(ProductRepository $productRepo){
        $this->productRepo = $productRepo;
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveAllProducts() {
        return $this->productRepo->findAll();
    }

    /**
     * {@inheritDoc}
     */
    public function findProductWithId($id) {
        return $this->productRepo->findOneBy(["id" => $id]);
    }


}