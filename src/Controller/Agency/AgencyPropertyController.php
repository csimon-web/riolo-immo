<?php

namespace App\Controller\Agency;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AgencyPropertyController extends AbstractController
{
    private $repo;
    private $em;

    public function __construct(PropertyRepository $repo, EntityManagerInterface $em)
    {
        $this->repo = $repo;
        $this->em = $em;
    }

    #[Route('/agency', name: 'app.agency.property.index')]
    public function index(): Response
    {
        $properties = $this->repo->findAll();

        return $this->render('agency/property/index.html.twig', compact('properties'));
    }

    #[Route('/agency/property', name: 'app.agency.property.new', methods:['GET', 'POST'])]
    public function new(Request $request)
    {
        // $this->denyAccessUnlessGranted('edit');
        $property = new Property();
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($property);
            $this->em->flush();
            $this->addFlash('success', 'Bien créé avec succès');
            return $this->redirectToRoute('app.agency.property.index');
        }

        return $this->render('agency/property/new.html.twig', [
            'property' => $property,
            'form'     => $form->createView()
        ]);
    }

    #[Route('/agency/property/{id}', name: 'app.agency.property.delete', methods:['DELETE'])]
    public function delete(Property $property, Request $request)
    {
        // $this->denyAccessUnlessGranted('delete', $property);
        if ($this->isCsrfTokenValid('delete' . $property->getId(), $request->get('_token'))) {
            $this->em->remove($property);
            $this->em->flush();
            $this->addFlash('success', 'Bien supprimé avec succès');
            // dump('Condition remplie');
        }
        return $this->redirectToRoute('app.agency.property.index');
    }

    #[Route('/agency/property/{id}', name: 'app.agency.property.edit', methods:['GET', 'PATCH'])]
    public function edit(Property $property, Request $request)
    {
        // $this->denyAccessUnlessGranted('edit', $property);
        $form = $this->createForm(PropertyType::class, $property, [
            'method' => 'PATCH'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Bien modifié avec succès');
            return $this->redirectToRoute('app.agency.property.index');
        }

        return $this->render('agency/property/edit.html.twig', [
            'property' => $property,
            'form'     => $form->createView()
        ]);
    }
}
