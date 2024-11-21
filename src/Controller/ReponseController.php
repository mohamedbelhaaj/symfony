<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Form\ReponseType;
use App\Repository\ReponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

#[Route('/admin/reponse')]
class ReponseController extends AbstractController
{
    private $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $transport = Transport::fromDsn($_ENV['MAILER_DSN']);
        $this->mailer = new Mailer($transport);
    }

    #[Route('/new/{id}', name: 'app_reponse_new', methods: ['GET', 'POST'])]
    public function new(Request $request,Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $reponse = new Reponse();
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamation->setStatus("Traitée");
            $reponse->setReclamation($reclamation);
            $entityManager->persist($reponse);
            $entityManager->flush();
            $this->sendResponseEmail($reclamation,$reponse);

            return $this->redirectToRoute('app_assistance', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reponse/new.html.twig', [
            'reponse' => $reponse,
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'app_reponse_show', methods: ['GET'])]
    public function show(Reponse $reponse): Response
    {
        return $this->render('reponse/show.html.twig', [
            'reponse' => $reponse,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_reponse_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reponse/edit.html.twig', [
            'reponse' => $reponse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reponse_delete', methods: ['POST'])]
    public function delete(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reponse->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reponse);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
    }

    private function sendResponseEmail(Reclamation $reclamation,Reponse $response)
    {
        $email = (new TemplatedEmail())
            ->from('assurancytn@gmail.com')
            ->to($reclamation->getUser()->getEmail())
            ->subject('Response from assurancy.tn | Reclamation'.$reclamation->getId())
            ->htmlTemplate("email/emailReponse.html.twig")
            ->context([
                'Username' => $reclamation->getUser()->getPrenom()." ".$reclamation->getUser()->getNom(),
                'recid' => $reclamation->getId(),
                'reponse' => $response->getDescription(),
            ]);
            $loader = new FilesystemLoader(__DIR__.'/../../templates');
            $twigEnv = new Environment($loader);
            $twigBodyRenderer = new BodyRenderer($twigEnv);
            $twigBodyRenderer->render($email);
        $this->mailer->send($email);
    }
}
