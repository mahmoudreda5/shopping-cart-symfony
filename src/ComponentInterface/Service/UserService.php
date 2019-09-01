<?php 

namespace App\ComponentInterface\Service;
use App\Repository\UserRepository;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;



class UserService{

    private $userRepo;
    private $entityManager;

    public function __construct(UserRepository $userRepo, EntityManagerInterface $entityManager){
        $this->userRepo = $userRepo;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function findUserWithPSID($PSID){
        return $this->userRepo->findOneBy(["name" => $PSID]);
    }

    /**
     * {@inheritDoc}
     */
    public function createUserWithPSID($PSID){
        $user = new User();
        $user->setName($PSID);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function findUserWithPhone($phone) {
        return $this->userRepo->findOneBy(["phone" => $phone]);
    }


}