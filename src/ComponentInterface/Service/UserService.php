<?php 

namespace App\ComponentInterface\Service;
use App\Repository\UserRepository;



class UserService implements UserServiceInterface{

    private $userRepo;
    public function __construct(UserRepository $userRepo){
        $this->userRepo = $userRepo;
    }

    /**
     * {@inheritDoc}
     */
    public function findUserWithPhone($phone) {
        return $this->userRepo->findOneBy(["phone" => $phone]);
    }


}