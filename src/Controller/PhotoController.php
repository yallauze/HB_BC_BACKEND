<?php

namespace App\Controller;

use App\Repository\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PhotoController extends AbstractController
{
    /**
     * @Route("/advert/photo/{advertId}", name="photo", methods={"GET"})
     */
    public function getAllPhotosByAdvert(PhotoRepository $photoRepository, int $advertId)
    {
        $photos = $photoRepository->findPhotosByAdvertId($advertId);
        return $this->json($photos, 200);
    }
}

