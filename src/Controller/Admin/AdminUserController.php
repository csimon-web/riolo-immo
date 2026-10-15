<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/administration')]
#[IsGranted('ROLE_ADMIN')]
class AdminUserController extends AbstractController
{
    #[Route('/utilisateurs', name: 'app.admin.user.index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy([], [
            'lastName' => 'ASC',
            'firstName' => 'ASC',
        ]);

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/utilisateur/nouveau', name: 'app.admin.user.new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new User();

        $form = $this->createForm(UserType::class, $user, [
            'is_edit' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            $user->setPassword(
                $passwordHasher->hashPassword($user, $plainPassword)
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur créé avec succès.');

            return $this->redirectToRoute('app.admin.user.index');
        }

        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route(
        '/utilisateur/{id}/modification',
        name: 'app.admin.user.edit',
        methods: ['GET', 'POST'],
        requirements: ['id' => '\d+']
    )]
    public function edit(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $form = $this->createForm(UserType::class, $user, [
            'is_edit' => true,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            if ($plainPassword !== null && $plainPassword !== '') {
                $user->setPassword(
                    $passwordHasher->hashPassword($user, $plainPassword)
                );
            }

            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur modifié avec succès.');

            return $this->redirectToRoute('app.admin.user.index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route(
        '/utilisateur/{id}',
        name: 'app.admin.user.delete',
        methods: ['POST'],
        requirements: ['id' => '\d+']
    )]
    public function delete(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($user === $this->getUser()) {
            $this->addFlash(
                'danger',
                'Vous ne pouvez pas supprimer votre propre compte.'
            );

            return $this->redirectToRoute('app.admin.user.index');
        }

        if ($this->isCsrfTokenValid(
            'delete' . $user->getId(),
            $request->request->get('_token')
        )) {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        }

        return $this->redirectToRoute('app.admin.user.index');
    }
}