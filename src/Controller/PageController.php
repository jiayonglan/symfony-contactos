<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Contacto;
use Doctrine\Persistence\ManagerRegistry;

final class PageController extends AbstractController
{
    #[Route('/page', name: 'app_page')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PageController.php',
        ]);
    }

    #[Route('/', name: 'inicio')]
    public function inicio(ManagerRegistry $doctrine): Response
    {
    $repositorio = $doctrine->getRepository(Contacto::class);
    // findAll es un método que se encuentra en el repositorio
    $contactos = $repositorio->findAll();

    //Mostramos la plantilla pasándole los contactos
    return $this->render("inicio.html.twig", ["contactos" => $contactos]);

    }

}
