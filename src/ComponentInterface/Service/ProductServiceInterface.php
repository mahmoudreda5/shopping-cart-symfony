<?php 

namespace App\ComponentInterface\Service;


interface ProductServiceInterface{


    /**
     * retrieve all products
     * 
     * @param
     * @return mixed
     */
    public function retrieveAllProducts();

    /**
     * find product with id
     * 
     * @param mixed $id
     * @return mixed
     */
    public function findProductWithId($id);

}