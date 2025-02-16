<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $response = new Response(
            $this->renderView('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]),
            200
        );

        $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-site');
        $response->headers->set('Origin-Trial', 'bAsAAACBeyJvcmlnaW4iOiJodHRwczovL3d3dy5yYW1pcmV6dmFsbGUuY29tLm14OjQ0MyIsImZlYXR1cmUiOiJVbnJlc3RyaWN0ZWRTaGFyZWRBcnJheUJ1ZmZlciIsImV4cGlyeSI6MTYzMzQ3ODM5OSwiaXNTdWJkb21haW4iOnRydWV9');


        return $response;
    }
}
