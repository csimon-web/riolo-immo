<?php

namespace App\Controller;

use App\Form\Contact2Type;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app.contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(Contact2Type::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $email = $data['email'];
            $content = $data['content'];

            $email = (new Email())
                ->from($email)
                ->to('admin@admin.com')
                ->subject('Demande de contact')
                ->text($content)
            ;

            $mailer->send($email);
        }

        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'form' => $form
        ]);
    }
}
