<?php

namespace App\Controller;

use App\Repository\FuelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FuelController extends AbstractController
{
    /**
     * @Route("/fuel", name="fuel", methods={"GET"})
     */
    public function getFuels(FuelRepository $fuelRepository){
        $fuels = $fuelRepository->findFuels();
        return $this->json($fuels, 200);
    }

}
