<?php

namespace App\Controller\Admin;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminPropertyController extends AbstractController
{
    #[Route('/administration/biens', name: 'app.admin.property.index')]
    public function index(PropertyRepository $propertyRepository): Response
    {
        $properties = $propertyRepository->findAll();

        return $this->render('admin/property/index.html.twig', [
            'properties' => $properties,
        ]);
    }

    #[Route('/administration/bien/nouveau', name: 'app.admin.property.new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // $this->denyAccessUnlessGranted('edit');

        $property = new Property();
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($property);
            $entityManager->flush();

            $this->addFlash('success', 'Bien créé avec succès');

            return $this->redirectToRoute('app.admin.property.index');
        }

        return $this->render('admin/property/new.html.twig', [
            'property' => $property,
            'form' => $form->createView(),
        ]);
    }

    #[Route(
        '/administration/bien/{id}',
        name: 'app.admin.property.delete',
        methods: ['DELETE'],
        requirements: ['id' => '\d+']
    )]
    public function delete(
        Property $property,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // $this->denyAccessUnlessGranted('delete', $property);

        if ($this->isCsrfTokenValid(
            'delete' . $property->getId(),
            $request->request->get('_token')
        )) {
            $entityManager->remove($property);
            $entityManager->flush();

            $this->addFlash('success', 'Bien supprimé avec succès');
        }

        return $this->redirectToRoute('app.admin.property.index');
    }

    #[Route(
        '/administration/bien/{id}/modification',
        name: 'app.admin.property.edit',
        methods: ['GET', 'PATCH'],
        requirements: ['id' => '\d+']
    )]
    public function edit(
        Property $property,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // $this->denyAccessUnlessGranted('edit', $property);

        $form = $this->createForm(PropertyType::class, $property, [
            'method' => 'PATCH',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Bien modifié avec succès');

            return $this->redirectToRoute('app.admin.property.index');
        }

        return $this->render('admin/property/edit.html.twig', [
            'property' => $property,
            'form' => $form->createView(),
        ]);
    }
}