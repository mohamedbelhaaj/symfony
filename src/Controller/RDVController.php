<?php

namespace App\Controller;

use App\Entity\RDV;
use App\Entity\Utilisateur;
use App\Form\RDVType;
use App\Repository\RDVRepository;
use Doctrine\ORM\EntityManagerInterface;
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

class RDVController extends AbstractController
{

    private $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $transport = Transport::fromDsn($_ENV['MAILER_DSN']);
        $this->mailer = new Mailer($transport);
    }

    #[Route('/client/rdv/new', name: 'app_r_d_v_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rDV = new RDV();
        $rDV->setDate(new \DateTime('now'));
        $form = $this->createForm(RDVType::class, $rDV);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user_id = $request->getSession()->get('user_id');
            $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['id' => $user_id]);
            $rDV->setUser($user);
            $rDV->setStatus("Non Confirmée");
            $entityManager->persist($rDV);
            $entityManager->flush();

            return $this->redirectToRoute('assistance', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rdv/new.html.twig', [
            'r_d_v' => $rDV,
            'form' => $form,
        ]);
    }

    #[Route('/admin/rdv/show/{id}', name: 'app_r_d_v_show', methods: ['GET'])]
    public function show(RDV $rDV): Response
    {
        return $this->render('rdv/show.html.twig', [
            'r_d_v' => $rDV,
        ]);
    }

    #[Route('/admin/rdv/edit/{id}', name: 'app_r_d_v_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RDV $rDV, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RDVType::class, $rDV);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_assistance', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rdv/edit.html.twig', [
            'r_d_v' => $rDV,
            'form' => $form,
        ]);
    }

    #[Route('/admin/rdv/confirm/{id}', name: 'app_r_d_v_confirm', methods: ['GET', 'POST'])]
    public function confirm(Request $request, RDV $rDV, EntityManagerInterface $entityManager): Response
    {
            $rDV->setStatus("Confirmée");
            $entityManager->flush();
            $this->sendResponseEmail($rDV);

            return $this->redirectToRoute('app_assistance', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/rdv/{id}', name: 'app_r_d_v_delete', methods: ['POST'])]
    public function delete(Request $request, RDV $rDV, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rDV->getId(), $request->request->get('_token'))) {
            $entityManager->remove($rDV);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_assistance', [], Response::HTTP_SEE_OTHER);
    }

    private function sendResponseEmail(RDV $rdv)
{
    $formattedDate = $rdv->getDate()->format('d-m-Y H:i');

    $email = (new TemplatedEmail())
        ->from('assurancytn@gmail.com')
        ->to($rdv->getUser()->getEmail())
        ->subject('Response from assurancy.tn | Rendez-vous°'.$rdv->getId())
        ->htmlTemplate("email/emailRDV.html.twig")
            ->context([
                'Username' => $rdv->getUser()->getPrenom()." ".$rdv->getUser()->getNom(),
                'rdvid' => $rdv->getId(),
                'date' => $formattedDate,
            ]);
            $loader = new FilesystemLoader(__DIR__.'/../../templates');
            $twigEnv = new Environment($loader);
            $twigBodyRenderer = new BodyRenderer($twigEnv);
            $twigBodyRenderer->render($email);
    $this->mailer->send($email);
}

}
