<?php

namespace App\Controller;

use App\Repository\ProfessionalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfessionalController extends AbstractController
{
    /**
     * @Route("/admin/professional", name="admin_professionals", methods={"GET"})
     */
    public function adminGetProfessionals(ProfessionalRepository $professionalRepository)
    {
        $professionals = $professionalRepository->findProfessionals();
        return $this->json($professionals, 200);
    }

    /**
     * @Route("/admin/professional/total", name="admin_professional_total", methods={"GET"})
     */
    public function adminGetProfessionalsTotal(ProfessionalRepository $professionalRepository)
    {
        $nb_professional = $professionalRepository->findProfessionalsTotalNumber();
        return $this->json($nb_professional, 200);
    }

    /**
     * @Route("/admin/professional/{proId}", name="admin_professional", methods={"GET"})
     */
    public function adminGetProfessionalById(ProfessionalRepository $professionalRepository, int $proId)
    {
        $professional = $professionalRepository->findProfessionalById($proId);
        return $this->json($professional, 200);
    }

    /**
     * @Route("/admin/professional/{deleteProId}", name="admin_professional_delete", methods={"DELETE"})
     */
    public function adminDeleteProfessionalById(
        EntityManagerInterface $entityManager,
        int $deleteProId,
        ProfessionalRepository $professionalRepository
    ) {
        // use the professional id to get the user id
        $deletePro = $professionalRepository->find($deleteProId);
        $deleteUser = $deletePro->getUser();
        $entityManager->remove($deleteUser); // user delete will delete the related professional
        $entityManager->flush();
        return $this->json(["message" => "success"], 200);
    }

    /**
     * @Route("/admin/professional/{editProId}", name="admin_professional_edit", methods={"PUT"})
     */
    public function adminUpdateProfessionalById(
        Request $request,
        int $editProId,
        EntityManagerInterface $entityManager,
        ProfessionalRepository $professionalRepository,
        UserPasswordEncoderInterface $passwordEncoder
    ) {

        $postedEditProInfo = json_decode($request->getContent(), false); // get the json object to be updated
        $editPro = $professionalRepository->find($editProId); // find the pro to be updated
        $editProUser = $editPro->getUser(); // find the pro's user account
        // update the pro's user account
        $editProUser->setUsername($postedEditProInfo->username);
        if (property_exists($postedEditProInfo, "password")) {
            $editProUser->setPassword($passwordEncoder->encodePassword($editProUser, $postedEditProInfo->password));
        }
        $entityManager->flush();
        // update the pro's info
        $editPro->setFirstname($postedEditProInfo->firstname);
        $editPro->setLastname($postedEditProInfo->lastname);
        $editPro->setEmail($postedEditProInfo->email);
        $editPro->setTel($postedEditProInfo->tel);
        $editPro->setSiretNumber($postedEditProInfo->siretNumber);
        $entityManager->flush();
        return $this->json(["message" => "success"], 200);
    }



    /**
     * @Route("/pro/professional", name="pro_professional_self", methods={"GET"})
     */
    public function proGetProfessionalSelf(ProfessionalRepository $professionalRepository)
    {
        $proUserId = $this->getUser()->getId();
        $proInfo = $professionalRepository->findProfessionalByUserId($proUserId);
        return $this->json($proInfo, 200);
    }


    /**
     * @Route("/pro/professional", name="pro_professional_edit", methods={"PUT"})
     */
    public function proUpdateUserInfoById(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $postedEditProInfo = json_decode($request->getContent(), false); // get the json object to be updated
        $editProUser = $this->getUser(); // find the pro's user account
        $editPro = $editProUser->getProfessional(); // loggedin user's pro is the pro to be editted

        // update the pro's user account
        $editProUser->setUsername($postedEditProInfo->username);
        if (property_exists($postedEditProInfo, "password")) {
            $editProUser->setPassword($passwordEncoder->encodePassword($editProUser, $postedEditProInfo->password));
        }
        $entityManager->flush();
        // update the pro's info
        $editPro->setFirstname($postedEditProInfo->firstname);
        $editPro->setLastname($postedEditProInfo->lastname);
        $editPro->setEmail($postedEditProInfo->email);
        $editPro->setTel($postedEditProInfo->tel);
        $editPro->setSiretNumber($postedEditProInfo->siretNumber);
        $entityManager->flush();
        return $this->json(["message" => "success"], 200);
    }
}
