<?php

namespace App\Controller;

use App\Entity\Contacto;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ContactoController extends AbstractController
{
    #[Route(
        '/contacto/{codigo}',
        name: 'contacto',
        requirements: ['codigo' => '[0-9]+'],
        defaults: ['codigo' => 1]
    )]
    public function ficha(
        ManagerRegistry $doctrine,
        int $codigo
    ): Response {
        $repositorio = $doctrine->getRepository(Contacto::class);

        $contacto = $repositorio->find($codigo);

        return $this->render('ficha_contacto.html.twig', [
            'contacto' => $contacto
        ]);
    }

    #[Route('/contacto/nuevo/{nombre}/{telefono}/{email}', name: 'nuevo')]
    public function nuevo(
        ManagerRegistry $doctrine,
        string $nombre,
        string $telefono,
        string $email
    ): Response {

        $contacto = new Contacto();

        $contacto->setNombre($nombre);
        $contacto->setTelefono($telefono);
        $contacto->setEmail($email);

        $entityManager = $doctrine->getManager();
        $entityManager->persist($contacto);
        $entityManager->flush();

        return $this->redirectToRoute('contacto', [
            'codigo' => $contacto->getId()
        ]);
    }

    #[Route('/contacto/empieza/{letra}', name: 'empieza-por')]
    public function empieza(ManagerRegistry $doctrine, string $letra)
    {
    $repositorio = $doctrine->getRepository(Contacto::class);

    $contactos = $repositorio->startsWith($letra);

    return $this->render('lista_contactos.html.twig', [

        'contactos' => $contactos,

        'letra' => $letra,

    ]);

    }

    public function modificar (ManagerRegistry $doctrine, int $codigo, string $nombre_nuevo)
    {
        $contacto = $doctrine->getRepository(Contacto::class)->find($codigo);
        if  ($contacto){
            $contacto->setNombre($nombre_nuevo);
            $entityManager = $doctrine->getManager();
            try{
                $entityManager->persist($contacto);
                $entityManager->flush();

                return $this->redirectToRoute('contacto', [
                    'codigo' => $contacto->getId()
                ]);
            } catch (\Exception $e){
                error_log("Error insertando objeto " . $e->getMessage());

                return new Response("Error insertando objeto " . $e->getMessage());
            }
        }
        return $this->redirectToRoute('contacto', ["codigo" => null]);
    }
}