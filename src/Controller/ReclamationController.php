<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Entity\Utilisateur;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use App\Repository\ReponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReclamationController extends AbstractController
{

    #[Route('/client/reclamation/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user_id = $request->getSession()->get('user_id');
            
            $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['id' => $user_id]);
            $email=$user->getEmail();
            $reclamation->setEmail($email);
            $tel =$user->getTel();
            $reclamation->setTel($tel);

            $reclamation->setUser($user);
            $reclamation->setStatus("En Attente");
            $entityManager->persist($reclamation);
            $entityManager->flush();

            return $this->redirectToRoute('assistance', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/admin/reclamation/show/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation, ReponseRepository $reponseRepository): Response
    {
        $reponses = $reponseRepository->findBy(['reclamation' => $reclamation]);
        if(count($reponses)>0){
            $reponse=$reponses[0];
            return $this->render('reclamation/show.html.twig', [
                'reclamation' => $reclamation,
                'reponse'=>$reponse
            ]);
        }
        else{
            $reponse= null;
            return $this->render('reclamation/show.html.twig', [
                'reclamation' => $reclamation,
                'reponse'=>$reponse
            ]);
        }

    }

    #[Route('/admin/reclamation/edit/{id}', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_assistance', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/admin/reclamation/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
            $reponse = $entityManager->getRepository(Reponse::class)->findOneBy(['reclamation' => $reclamation]);
            if ($reponse){
                $entityManager->remove($reponse);
            }
            $entityManager->remove($reclamation);
            $entityManager->flush();

        return $this->redirectToRoute('app_assistance', [], Response::HTTP_SEE_OTHER);
    }
}
