<?php

namespace App\Controller;

use App\Repository\ModelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ModelController extends AbstractController
{
    /**
     * @Route("/model", name="model", methods={"GET"})
     */
    public function getModels(ModelRepository $modelRepository){
        $models = $modelRepository->findModels();
        return $this->json($models, 200);
    }

}
