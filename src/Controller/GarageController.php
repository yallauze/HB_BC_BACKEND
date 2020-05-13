<?php

namespace App\Controller;

use App\Entity\Garage;
use App\Repository\GarageRepository;
use App\Repository\ProfessionalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class GarageController extends AbstractController
{
    /**
     * @Route("/admin/garage", name="admin_garage", methods={"GET"})
     */
    public function adminGetGarages(GarageRepository $garageRepository)
    {
        $garages = $garageRepository->findAllGarages();
        return $this->json($garages, 200);
    }
    /**
     * @Route("/admin/garage/total", name="admin_garage_total", methods={"GET"})
     */
    public function adminGetGaragesTotal(GarageRepository $garageRepository)
    {
        $nb_garage = $garageRepository->findGaragesTotalNumber();
        return $this->json($nb_garage, 200);
    }

    /**
     * @Route("/admin/garage/pro/{proId}", name="admin_garage_by_proId", methods={"GET"})
     */
    public function adminGetGaragesByProId(GarageRepository $garageRepository, int $proId)
    {
        $garages = $garageRepository->findAllGaragesByProId($proId);
        return $this->json($garages, 200);
    }

    /**
     * @Route("/admin/garage/{garageId}", name="admin_garage_by_garageId", methods={"GET"})
     */
    public function adminGetGarageByGarageId(GarageRepository $garageRepository, int $garageId)
    {
        $garage = $garageRepository->findOneGarageByGarageId($garageId);
        return $this->json($garage, 200);
    }

    /**
     * @Route("/admin/garage/pro/{proId}", name="admin_add_garage_by_proId", methods={"POST"})
     */
    public function adminAddGarageByProId(
        ProfessionalRepository $professionalRepository,
        int $proId,
        SerializerInterface $serializer,
        Request $request,
        EntityManagerInterface $entityManager
    ) {
        $postedData = $request->getContent();
        $postedNewGarage = $serializer->deserialize($postedData, Garage::class, 'json');
        $postedNewGarage->setProfessional($professionalRepository->find($proId));
        $entityManager->persist($postedNewGarage);
        $entityManager->flush();
        return $this->json(["message" => "Garage created!!"], 201);
    }

    /**
     * @Route("/admin/garage/{garageId}", name="admin_update_garage_by_garageId", methods={"PUT"})
     */
    public function adminUpdateGarageByGarageId(GarageRepository $garageRepository, int $garageId, Request $request, EntityManagerInterface $entityManager)
    {
        $postedData = json_decode($request->getContent(), false); //posted new info
        $editGarage = $garageRepository->find($garageId); // find it in the database
        $editGarage->setName($postedData->name); // assigne all the new values
        $editGarage->setTel($postedData->tel); // assigne all the new values
        $editGarage->setStreet($postedData->street); // assigne all the new values
        $editGarage->setCity($postedData->city); // assigne all the new values
        $editGarage->setPostcode($postedData->postcode); // assigne all the new values
        $editGarage->setCountry($postedData->country); // assigne all the new values
        $entityManager->flush();
        return $this->json(["message" => "success"], 200);
    }

    /**
     * @Route("/admin/garage/{garageId}", name="admin_delete_garage_by_garageId", methods={"DELETE"})
     */
    public function adminDeleteGarageByGarageId(GarageRepository $garageRepository, int $garageId, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($garageRepository->find($garageId));
        $entityManager->flush();
        return $this->json(["message" => "success"], 200);
    }




    /**
     * @Route("/pro/garage", name="pro_get_garages", methods={"GET"})
     */
    public function proGetGarages(GarageRepository $garageRepository)
    {
        // use the loggedin user's proId to 
        $proId = $this->getUser()->getProfessional()->getId();
        $garages = $garageRepository->findAllGaragesByProId($proId);
        return $this->json($garages, 200);
    }

    /**
     * @Route("/pro/garage/{garageId}", name="pro_get_garage_by_id", methods={"GET"})
     */
    public function proGetGarageByGarageId(GarageRepository $garageRepository, int $garageId)
    {
        $garage = $garageRepository->findOneGarageByGarageId($garageId);
        $garageUserId = $garageRepository->find($garageId)->getProfessional()->getUser()->getId();
        if ($garageUserId === $this->getUser()->getId()) {
            return $this->json($garage, 200);
        } else {
            return $this->json(["message" => "access limited"], 402);
        }
    }

    /**
     * @Route("/pro/garage", name="pro_add_garage", methods={"POST"})
     */
    public function proAddGarage(
        SerializerInterface $serializer,
        Request $request,
        EntityManagerInterface $entityManager
    ) {
        $postedData = $request->getContent();
        $postedNewGarage = $serializer->deserialize($postedData, Garage::class, 'json');
        $postedNewGarage->setProfessional($this->getUser()->getProfessional());
        $entityManager->persist($postedNewGarage);
        $entityManager->flush();
        return $this->json(["message" => "Garage created!!"], 201);
    }

    /**
     * @Route("/pro/garage/{garageId}", name="pro_update_garage_by_id", methods={"PUT"})
     */
    public function proUpdateGarageByGarageId(GarageRepository $garageRepository, int $garageId, Request $request, EntityManagerInterface $entityManager)
    {
        $postedData = json_decode($request->getContent(), false); //posted new info
        $editGarage = $garageRepository->find($garageId); // find it in the database
        if ($editGarage->getProfessional()->getUser()->getId() === $this->getUser()->getId()) {
            $editGarage->setName($postedData->name); // assigne all the new values
            $editGarage->setTel($postedData->tel); // assigne all the new values
            $editGarage->setStreet($postedData->street); // assigne all the new values
            $editGarage->setCity($postedData->city); // assigne all the new values
            $editGarage->setPostcode($postedData->postcode); // assigne all the new values
            $editGarage->setCountry($postedData->country); // assigne all the new values
            $entityManager->flush();
            return $this->json(["message" => "success"], 200);
        } else {
            return $this->json(["message" => "access limited"], 402);
        }
    }

    /**
     * @Route("/pro/garage/{garageId}", name="pro_delete_garage_by_id", methods={"DELETE"})
     */
    public function proDeleteGarageByGarageId(GarageRepository $garageRepository, int $garageId, EntityManagerInterface $entityManager)
    {
        $garage = $garageRepository->find($garageId);
        $garageProUserId = $garage->getProfessional()->getUser()->getId();
        if ($garageProUserId === $this->getUser()->getId()) {
            $entityManager->remove($garage);
            $entityManager->flush();
            return $this->json(["message" => "success"], 200);
        } else {
            return $this->json(["message" => "access limited"], 402);
        }
    }
}
