<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Property;
use App\Form\ContactType;
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
use App\Repository\PropertyRepository;
use App\Notification\ContactNotification;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PropertyController extends AbstractController
{
    private $repo;

    public function __construct(PropertyRepository $repo)
    {
        $this->repo = $repo;
    }

    #[Route('/biens', name: 'app.property.index')]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $search = new PropertySearch;
        $form = $this->createForm(PropertySearchType::class, $search);
        $form->handleRequest($request);

        $properties = $paginator->paginate(
            $this->repo->findAllVisibleQuery($search),
            $request->query->getInt('page', 1),
            9
        );
        return $this->render('property/index.html.twig', [
            'current_menu' => 'properties',
            'properties' => $properties,
            'form' => $form->createView()
        ]);
    }

    #[Route('/bien/{slug}-{id}', name: 'app.property.show', requirements: ["slug" => "[a-z0-9\-]*"])]
    public function show(Property $property, string $slug, Request $request, ContactNotification $notification): Response
    {
        if ($property->getSlug() !== $slug) {
            return $this->redirectToRoute('app.property.show', [
                'id' => $property->getId(),
                'slug' => $property->getSlug()
            ], 301);
        }

        $contact = new Contact();
        $contact->setProperty($property);
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $notification->notify($contact);
            $this->addFlash('success', 'Votre email a bien été envoyé');
            return $this->redirectToRoute('app.property.show', [
                'id'   => $property->getId(),
                'slug' => $property->getSlug()
            ]);
        }

        return $this->render('property/show.html.twig', [
            'property' => $property,
            'current_menu' => 'properties',
            'form'         => $form->createView()
        ]);
    }
}
