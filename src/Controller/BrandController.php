<?php

namespace App\Controller;

use App\Repository\BrandRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BrandController extends AbstractController
{
    /**
     * @Route("/brand", name="brand", methods={"GET"})
     */
    public function getBrands(BrandRepository $brandRepository){
        $brands = $brandRepository->findBrands();
        return $this->json($brands, 200);

    }

}
