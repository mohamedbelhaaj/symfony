<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Entity\Rating;
use App\Form\DemandeType;
use App\Repository\DemandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Utilisateur;
use App\Form\RatingType;
use App\Repository\MechanoRepository;
use Doctrine\ORM\EntityManager;

class DemandeController extends AbstractController
{
    #[Route('/client/demande/new', name: 'app_demande_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
{
    $demande = new Demande();
    $form = $this->createForm(DemandeType::class, $demande);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        //$user_id = $request->getSession()->get('user_id');
        $user_id = $request->getSession()->get('user_id');
        $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['id' => $user_id]);
        $latitude = $request->request->get('latitude');
        $longitude = $request->request->get('longitude');
        $demande->setLocalisation($latitude.",".$longitude);
        $demande->setStatus("En Attente");
        $demande->setUser($user);
        // $user = $this->getUser();
        // $demande->setIdUser($user);
        $entityManager->persist($demande);
        $entityManager->flush();
        return $this->redirectToRoute('app_demande_showC', ['id' => $demande->getId()], Response::HTTP_SEE_OTHER);
    }
    return $this->renderForm('demande/new.html.twig', [
        'demande' => $demande,
        'form' => $form,
    ]);
}

    #[Route('/client/Ma-demande/{id}', name: 'app_demande_showC', methods: ['GET','POST'])]
    public function showC(Request $request, Demande $demande,EntityManagerInterface $entityManager): Response
    {
        $rating = $entityManager->getRepository(Rating::class)->findOneBy(['id' => $demande->getRating()]);
        $formR = $this->createForm(RatingType::class, $rating);
        $formR->handleRequest($request);
        if ($formR->isSubmitted() && $formR->isValid()) {
            // Enregistrez le nouvel avis dans la base de données
            $demande->setShowagain(false);
            $entityManager->flush();
        }
        return $this->render('demande/showC.html.twig', [
            'demande' => $demande,
            'formR' => $formR->createView(),
        ]);
    }

    #[Route('/admin/demande/accept', name: 'app_demande_accept', methods: ['POST'])]
    public function accept(EntityManagerInterface $entityManager, Request $request): Response
    {
        //$sos_id = $request->getSession()->get('user_id');
        $rating = new Rating();
        $estimatedTime = $request->request->get('estimatedTime');
        $id = $request->request->get('id');
        $demande = $entityManager->getRepository(Demande::class)->findOneBy(['id' => $id]);
        $sos_id = $request->getSession()->get('user_id');
        $sos = $entityManager->getRepository(Utilisateur::class)->findOneBy(['id' => $sos_id]);
        $demande_a = $entityManager->getRepository(Demande::class)->findOneBy(['agent' => $sos, 'status' => "Acceptée"]);
        if ($demande->getStatus() == "En Attente") {
        if ($demande_a){
            $this->addFlash('error',message:"Vous ne pouvez pas accepter deux demandes en même temps!");
        } else {
            $user_id = $request->getSession()->get('user_id');
            $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['id' => $user_id]);
            $rating->setUser($user);
            $rating->setNote(0);
            $rating->setCommentaire("");
            $demande->setRating($rating);
            $demande->setStatus("Acceptée");
            $demande->setAgent($sos);
            $demande->setTempsEstime($estimatedTime);
            $entityManager->persist($rating);
            $entityManager->flush();
        }
        }
        else {
            $this->addFlash('error',message:"Vous ne pouvez pas accepter une demande déja accepté ou fini!");
        }
        return $this->redirectToRoute('app_sos', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/demande/terminer/{id}', name: 'app_demande_end', methods: ['GET'])]
    public function terminer(Demande $demande,EntityManagerInterface $entityManager): Response
    {
        if ($demande->getStatus() == "Acceptée") {
            $demande->setStatus("Terminée");
            $entityManager->flush();
        }
        else {
            $this->addFlash('error',message:"Vous ne pouvez pas terminer une demande déja fini ou en attente!");
        }
        return $this->redirectToRoute('app_sos', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/demande/show/{id}', name: 'app_demande_show', methods: ['GET'])]
    public function show(Demande $demande): Response
    {
        return $this->render('demande/show.html.twig', [
            'demande' => $demande,
        ]);
    }

    #[Route('/admin/demande/edit/{id}', name: 'app_demande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Demande $demande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DemandeType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_sos', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('demande/edit.html.twig', [
            'demande' => $demande,
            'form' => $form,
        ]);
    }

    #[Route('/admin/demande/delete/{id}', name: 'app_demande_delete', methods: ['GET'])]
    public function delete(Demande $demande, EntityManagerInterface $entityManager): Response
    {
            $entityManager->remove($demande);
            $entityManager->flush();

        return $this->redirectToRoute('app_sos', [], Response::HTTP_SEE_OTHER);
    }
}
