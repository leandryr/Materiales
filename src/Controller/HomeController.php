<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home/reclamo", name="home_reclamo")
     */
    public function reclamo(): Response
    {
        return $this->render('home/reclamo.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/home/registro", name="home_registro")
     */
    public function registro(): Response
    {
        return $this->render('home/registro.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/home/historial", name="home_historial")
     */
    public function historial(): Response
    {
        return $this->render('home/historial.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
