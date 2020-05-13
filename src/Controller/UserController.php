<?php

namespace App\Controller;

use App\Entity\Professional;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(Request $request)
    {
        $user = $this->getUser();
        return $this->json([
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
            'apiToken' => $user->getApiToken()
        ]);
    }

    /**
     * @Route("/admin/user", name="admin_user_add", methods={"POST"})
     */
    public function addUser(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $postedUser = json_decode($request->getContent(), true);
        // create the user account
        $newUser = new User();    
        $newUser->setUsername($postedUser["firstname"].$postedUser["lastname"]);       
        $newUser->setPassword($passwordEncoder->encodePassword($newUser, $postedUser["firstname"]. "**" .$postedUser["lastname"]));
        $newUser->setRoles([$postedUser["roles"]]);
        $newUser->setApiToken(uniqid($postedUser["firstname"].$postedUser["lastname"]));
        $entityManager->persist($newUser);
        $entityManager->flush();

        if (in_array("ROLE_PRO", $newUser->getRoles())) {
            // this is a pro creation and his account creation
            $newProfessional = new Professional();
            $newProfessional->setFirstname($postedUser["firstname"]);
            $newProfessional->setLastname($postedUser["lastname"]);
            $newProfessional->setEmail($postedUser["email"]);
            $newProfessional->setTel($postedUser["tel"]);
            $newProfessional->setSiretNumber($postedUser["siretNumber"]);
            $newProfessional->setUser($newUser);
            $entityManager->persist($newProfessional);
            $entityManager->flush();
        }

        return $this->json([
            'username' => $newUser->getUsername(),
            'roles' => $newUser->getRoles(),
            'apiToken' => $newUser->getApiToken()
        ]);
    }
}
