<?php

namespace App\Controller;

use App\Entity\Contacto;
use App\Form\ContactoFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        Request $request,
        int $codigo
    ): Response {
        $repositorio = $doctrine->getRepository(Contacto::class);

        $contacto = $repositorio->find($codigo);

        if (!$contacto) {
            return $this->render('ficha_contacto.html.twig', [
                'contacto' => null
            ]);
        }

        // Si se ha pulsado el botón Editar
        if ($request->request->has('editar')) {

            if (!$this->getUser()) {
                return $this->redirectToRoute('inicio');
            }

            return $this->redirectToRoute('editar', [
                'codigo' => $contacto->getId()
            ]);
        }

        // Si se ha pulsado el botón Borrar
        if ($request->request->has('borrar')) {

            if (!$this->getUser()) {
                return $this->redirectToRoute('inicio');
            }

            $entityManager = $doctrine->getManager();

            $entityManager->remove($contacto);
            $entityManager->flush();

            return $this->redirectToRoute('inicio');
        }

        return $this->render('ficha_contacto.html.twig', [
            'contacto' => $contacto
        ]);
    }


    #[Route('/contacto/nuevo/{nombre}/{telefono}/{email}', name: 'nuevo2')]
    public function nuevo2(
        ManagerRegistry $doctrine,
        string $nombre,
        string $telefono,
        string $email
    ): Response {

        if (!$this->getUser()) {
            return $this->redirectToRoute('inicio');
        }

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
    public function empieza(
        ManagerRegistry $doctrine,
        string $letra
    ): Response {

        $repositorio = $doctrine->getRepository(Contacto::class);

        $contactos = $repositorio->startsWith($letra);

        return $this->render('lista_contactos.html.twig', [
            'contactos' => $contactos,
            'letra' => $letra,
        ]);
    }


    #[Route('/contacto/modificar/{codigo}/{nombre_nuevo}', name: 'modificar')]
    public function modificar(
        ManagerRegistry $doctrine,
        int $codigo,
        string $nombre_nuevo
    ): Response {

        if (!$this->getUser()) {
            return $this->redirectToRoute('inicio');
        }

        $contacto = $doctrine
            ->getRepository(Contacto::class)
            ->find($codigo);

        if ($contacto) {

            $contacto->setNombre($nombre_nuevo);

            $entityManager = $doctrine->getManager();

            try {

                $entityManager->persist($contacto);
                $entityManager->flush();

                return $this->redirectToRoute('contacto', [
                    'codigo' => $contacto->getId()
                ]);

            } catch (\Exception $e) {

                error_log(
                    "Error insertando objeto " . $e->getMessage()
                );

                return new Response(
                    "Error insertando objeto " . $e->getMessage()
                );
            }
        }

        return $this->redirectToRoute('inicio');
    }


    #[Route('/contacto/nuevo', name: 'nuevo')]
    public function nuevo(
        ManagerRegistry $doctrine,
        Request $request
    ): Response {

        // Comprobamos que el usuario está logueado
        if (!$this->getUser()) {
            return $this->redirectToRoute('inicio');
        }

        $contacto = new Contacto();

        $formulario = $this->createForm(
            ContactoFormType::class,
            $contacto
        );

        $formulario->handleRequest($request);

        if (
            $formulario->isSubmitted()
            && $formulario->isValid()
        ) {

            $contacto = $formulario->getData();

            $entityManager = $doctrine->getManager();

            $entityManager->persist($contacto);
            $entityManager->flush();

            return $this->redirectToRoute('contacto', [
                'codigo' => $contacto->getId()
            ]);
        }

        return $this->render('nuevo.html.twig', [
            'formulario' => $formulario->createView()
        ]);
    }


    #[Route(
        '/contacto/editar/{codigo}',
        name: 'editar',
        requirements: ['codigo' => '\d+']
    )]
    public function editar(
        ManagerRegistry $doctrine,
        Request $request,
        int $codigo
    ): Response {

        // Comprobamos que el usuario está logueado
        if (!$this->getUser()) {
            return $this->redirectToRoute('inicio');
        }

        $repositorio = $doctrine->getRepository(Contacto::class);

        $contacto = $repositorio->find($codigo);

        if ($contacto) {

            $formulario = $this->createForm(
                ContactoFormType::class,
                $contacto
            );

            $formulario->handleRequest($request);

            if (
                $formulario->isSubmitted()
                && $formulario->isValid()
            ) {

                $contacto = $formulario->getData();

                $entityManager = $doctrine->getManager();

                $entityManager->persist($contacto);
                $entityManager->flush();

                return $this->redirectToRoute('contacto', [
                    'codigo' => $contacto->getId()
                ]);
            }

            return $this->render('editar.html.twig', [
                'formulario' => $formulario->createView()
            ]);
        }

        return $this->render('ficha_contacto.html.twig', [
            'contacto' => null
        ]);
    }
}
