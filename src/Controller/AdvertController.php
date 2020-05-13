<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Entity\Photo;
use App\Repository\AdvertRepository;
use App\Repository\FuelRepository;
use App\Repository\GarageRepository;
use App\Repository\ModelRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdvertController extends AbstractController
{
    /**
     * @Route("/advert", name="advert", methods={"GET"})
     */
    public function getAllAdvertsForMainView(AdvertRepository $advertRepository)
    {
        $adverts = $advertRepository->findAllAdvertsForMainview(); //with limited info and one photo
        return $this->json($adverts, 200);
    }

    /**
     * @Route("/admin/advert/total", name="admin_advert_total", methods={"GET"})
     */
    public function adminGetAdvertsTotal(AdvertRepository $advertRepository)
    {
        $nb_advert = $advertRepository->findAdvertsTotalNumber();
        return $this->json($nb_advert, 200);
    }

    /**
     * @Route("/advert/{advertId}", name="advert_detail", methods={"GET"})
     */
    public function getOneAdvertByIdForDetailedView(AdvertRepository $advertRepository, int $advertId)
    {
        $advert = $advertRepository->find($advertId); //with limited info and all photos
        return $this->json($advert, 200, [], ['groups' => ['getAdvertByID']]);
    }

    /**
     * @Route("/admin/advert", name="admin_advert", methods={"GET"})
     */
    public function adminGetAdverts(AdvertRepository $advertRepository)
    {
        $adverts = $advertRepository->findAllAdverts();
        return $this->json($adverts, 200);
    }
    /**
     * @Route("/admin/advert/{advertId}", name="admin_advert_id", methods={"GET"})
     */
    public function adminGetAdvertById(AdvertRepository $advertRepository, int $advertId)
    {
        $advert = $advertRepository->findAdvertById($advertId);
        return $this->json($advert, 200);
    }
    /**
     * @Route("/admin/advert", name="admin_add_advert", methods={"POST"})
     */
    public function adminAddAdvert(
        Request $request,
        GarageRepository $garageRepository,
        FuelRepository $fuelRepository,
        ModelRepository $modelRepository,
        EntityManagerInterface $entityManager
    ) {
        $postedInfo = json_decode($request->getContent(), false);
        $newAdvert = new Advert();
        $newAdvert->setTitle($postedInfo->title);
        $newAdvert->setDescription($postedInfo->description);
        $newAdvert->setYearStartedAt($postedInfo->year_started_at);
        $newAdvert->setKm($postedInfo->km);
        $newAdvert->setPrice($postedInfo->price);
        $newAdvert->setCreatedAt(new DateTime());
        $newAdvert->setGarage($garageRepository->find($postedInfo->garage_id));
        $newAdvert->setFuel($fuelRepository->find($postedInfo->fuel_id));
        $newAdvert->setModel($modelRepository->find($postedInfo->model_id));
        // add advert to database
        $entityManager->persist($newAdvert);
        $entityManager->flush();
        // create in database new photos attached to this newly created advert
        foreach ($postedInfo->photos as $photo) {
            $newPhoto = new Photo();
            $newPhoto->setDataBase64($photo);
            $newPhoto->setAdvert($newAdvert);
            $entityManager->persist($newPhoto);
            $entityManager->flush();
        }
        return $this->json(["message" => "ok"], 200);
    }
    /**
     * @Route("/admin/advert/{advertId}", name="admin_update_advert", methods={"PUT"})
     */
    public function adminUpdateAdvertById(
        AdvertRepository $advertRepository,
        Request $request,
        FuelRepository $fuelRepository,
        ModelRepository $modelRepository,
        EntityManagerInterface $entityManager,
        int $advertId
    ) {
        $postedInfo = json_decode($request->getContent(), false);
        $editAdvert = $advertRepository->find($advertId);
        $editAdvert->setTitle($postedInfo->title);
        $editAdvert->setDescription($postedInfo->description);
        $editAdvert->setYearStartedAt($postedInfo->year_started_at);
        $editAdvert->setKm($postedInfo->km);
        $editAdvert->setPrice($postedInfo->price);
        //$editAdvert->setGarage($garageRepository->find($postedInfo->garage_id));
        $editAdvert->setFuel($fuelRepository->find($postedInfo->fuel_id));
        $editAdvert->setModel($modelRepository->find($postedInfo->model_id));
        $entityManager->flush();

        // delete all the olde photos of this advert
        foreach ($editAdvert->getPhotos() as $oldPhoto) {
            $entityManager->remove($oldPhoto);
            $entityManager->flush();
        }

        // create new photos to this advert
        foreach ($postedInfo->photos as $photo) {
            $newPhoto = new Photo();
            $newPhoto->setDataBase64($photo);
            $newPhoto->setAdvert($editAdvert);
            $entityManager->persist($newPhoto);
            $entityManager->flush();
        }
        return $this->json(["message" => "ok"], 200);
    }

    /**
     * @Route("/admin/advert/{advertId}", name="admin_delete_advert", methods={"DELETE"})
     */
    public function adminDeleteAdvertById(
        int $advertId,
        AdvertRepository $advertRepository,
        EntityManagerInterface $entityManager
    ) {
        $deleteAdvert = $advertRepository->find($advertId);
        $entityManager->remove($deleteAdvert);
        $entityManager->flush();
        return $this->json(["message" => "ok"], 200);
    }





    /**
     * @Route("/pro/advert", name="pro_advert", methods={"GET"})
     */
    public function proGetAdverts(AdvertRepository $advertRepository)
    {
        $proId = $this->getUser()->getProfessional()->getId();
        $adverts = $advertRepository->findAllAdvertsByProId($proId);
        return $this->json($adverts, 200);
    }
    /**
     * @Route("/pro/advert/{advertId}", name="pro_advert_id", methods={"GET"})
     */
    public function proGetAdvertById(AdvertRepository $advertRepository, int $advertId)
    {
        $advertUserId = $advertRepository->find($advertId)->getGarage()->getProfessional()->getUser()->getId();
        if ($advertUserId === $this->getUser()->getId()) {
            $advert = $advertRepository->findAdvertById($advertId);
            return $this->json($advert, 200);
        } else {
            return $this->json(["message" => "access limited"], 402);
        }
    }
    /**
     * @Route("/pro/advert", name="pro_add_advert", methods={"POST"})
     */
    public function proAddAdvert(
        Request $request,
        GarageRepository $garageRepository,
        FuelRepository $fuelRepository,
        ModelRepository $modelRepository,
        EntityManagerInterface $entityManager
    ) {
        $postedInfo = json_decode($request->getContent(), false);

        $postGarageId = $postedInfo->garage_id;

        $postGarageUserId = $garageRepository->find($postGarageId)->getProfessional()->getUser()->getId();

        if ($postGarageUserId === $this->getUser()->getId()) {
            $newAdvert = new Advert();
            $newAdvert->setTitle($postedInfo->title);
            $newAdvert->setDescription($postedInfo->description);
            $newAdvert->setYearStartedAt($postedInfo->year_started_at);
            $newAdvert->setKm($postedInfo->km);
            $newAdvert->setPrice($postedInfo->price);
            $newAdvert->setCreatedAt(new DateTime());
            $newAdvert->setGarage($garageRepository->find($postedInfo->garage_id));
            $newAdvert->setFuel($fuelRepository->find($postedInfo->fuel_id));
            $newAdvert->setModel($modelRepository->find($postedInfo->model_id));
            // Create in database the new advert           
            $entityManager->persist($newAdvert);
            $entityManager->flush();
            foreach ($postedInfo->photos as $photo) {
                $newPhoto = new Photo();
                $newPhoto->setDataBase64($photo);
                $newPhoto->setAdvert($newAdvert);
                // create in database new photos attached to this newly created advert
                $entityManager->persist($newPhoto);
                $entityManager->flush();
            }
            return $this->json(["message" => "it's ok"], 200);
        } else {
            return $this->json(["message" => "access limited"], 402);
        }
    }
    /**
     * @Route("/pro/advert/{advertId}", name="pro_update_advert", methods={"PUT"})
     */
    public function proUpdateAdvertById(
        AdvertRepository $advertRepository,
        Request $request,
        FuelRepository $fuelRepository,
        ModelRepository $modelRepository,
        EntityManagerInterface $entityManager,
        int $advertId
    ) {
        $advertUserId = $advertRepository->find($advertId)->getGarage()->getProfessional()->getUser()->getId();
        if ($advertUserId === $this->getUser()->getId()) {
            $postedInfo = json_decode($request->getContent(), false);
            $editAdvert = $advertRepository->find($advertId);
            $editAdvert->setTitle($postedInfo->title);
            $editAdvert->setDescription($postedInfo->description);
            $editAdvert->setYearStartedAt($postedInfo->year_started_at);
            $editAdvert->setKm($postedInfo->km);
            $editAdvert->setPrice($postedInfo->price);
            $editAdvert->setFuel($fuelRepository->find($postedInfo->fuel_id));
            $editAdvert->setModel($modelRepository->find($postedInfo->model_id));
            $entityManager->flush();

            // delete all the olde photos of this advert
            foreach ($editAdvert->getPhotos() as $oldPhoto) {
                $entityManager->remove($oldPhoto);
                $entityManager->flush();
            }

            // create new photos to this advert
            foreach ($postedInfo->photos as $photo) {
                $newPhoto = new Photo();
                $newPhoto->setDataBase64($photo);
                $newPhoto->setAdvert($editAdvert);
                $entityManager->persist($newPhoto);
                $entityManager->flush();
            }
            return $this->json(["message" => "ok"], 200);
        } else {
            return $this->json(["message" => "access limited"], 402);
        }
    }

    /**
     * @Route("/pro/advert/{advertId}", name="pro_delete_advert", methods={"DELETE"})
     */
    public function proDeleteAdvertById(
        int $advertId,
        AdvertRepository $advertRepository,
        EntityManagerInterface $entityManager
    ) {
        $advertUserId = $advertRepository->find($advertId)->getGarage()->getProfessional()->getUser()->getId();
        if ($advertUserId === $this->getUser()->getId()) {
            $deleteAdvert = $advertRepository->find($advertId);
            $entityManager->remove($deleteAdvert);
            $entityManager->flush();
            return $this->json(["message" => "ok"], 200);
        } else {
            return $this->json(["message" => "access limited"], 402);
        }
    }
}
