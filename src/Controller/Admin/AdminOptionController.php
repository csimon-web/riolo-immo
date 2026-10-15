<?php

namespace App\Controller\Admin;

use App\Entity\Option;
use App\Form\OptionType;
use App\Repository\OptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/administration')]
class AdminOptionController extends AbstractController
{
    #[Route('/options', name: 'app.admin.option.index', methods: ['GET'])]
    public function index(OptionRepository $optionRepository): Response
    {
        $options = $optionRepository->findAll();

        return $this->render('admin/option/index.html.twig', [
            'options' => $options,
        ]);
    }

    #[Route('/option/nouvelle', name: 'app.admin.option.new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $option = new Option();
        $form = $this->createForm(OptionType::class, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($option);
            $entityManager->flush();

            $this->addFlash('success', 'Option créée avec succès');

            return $this->redirectToRoute(
                'app.admin.option.index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('admin/option/new.html.twig', [
            'option' => $option,
            'form' => $form,
        ]);
    }

    #[Route(
        '/option/{id}/modification',
        name: 'app.admin.option.edit',
        methods: ['GET', 'POST'],
        requirements: ['id' => '\d+']
    )]
    public function edit(
        Request $request,
        Option $option,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(OptionType::class, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Option modifiée avec succès');

            return $this->redirectToRoute(
                'app.admin.option.index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('admin/option/edit.html.twig', [
            'option' => $option,
            'form' => $form,
        ]);
    }

    #[Route(
        '/option/{id}',
        name: 'app.admin.option.delete',
        methods: ['POST'],
        requirements: ['id' => '\d+']
    )]
    public function delete(
        Request $request,
        Option $option,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid(
            'delete' . $option->getId(),
            $request->request->get('_token')
        )) {
            $entityManager->remove($option);
            $entityManager->flush();

            $this->addFlash('success', 'Option supprimée avec succès');
        }

        return $this->redirectToRoute(
            'app.admin.option.index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}