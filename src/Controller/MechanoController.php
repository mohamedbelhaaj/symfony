<?php

namespace App\Controller;

use App\Entity\Mechano;
use App\Form\MechanoType;
use App\Repository\DemandeRepository;
use App\Repository\MechanoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MechanoController extends AbstractController
{

    #[Route('/admin/mechano/new', name: 'app_mechano_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $mechano = new Mechano();
        $form = $this->createForm(MechanoType::class, $mechano);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($mechano);
            $entityManager->flush();

            return $this->redirectToRoute('app_sos', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('mechano/new.html.twig', [
            'mechano' => $mechano,
            'form' => $form,
        ]);
    }

    #[Route('/admin/mechano/showMe/{id}', name: 'app_mechano_show', methods: ['GET'])]
    public function show(Mechano $mechano): Response
    {
        return $this->render('mechano/show.html.twig', [
            'mechano' => $mechano,
        ]);
    }

    #[Route('/admin/mechano/edit/{id}', name: 'app_mechano_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Mechano $mechano, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MechanoType::class, $mechano);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_sos', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('mechano/edit.html.twig', [
            'mechano' => $mechano,
            'form' => $form,
        ]);
    }

    #[Route('/admin/mechano/delete/{id}', name: 'app_mechano_delete', methods: ['GET'])]
    public function delete(Request $request, Mechano $mechano, EntityManagerInterface $entityManager): Response
    {
            $entityManager->remove($mechano);
            $entityManager->flush();

        return $this->redirectToRoute('app_sos', [], Response::HTTP_SEE_OTHER);
    }
}
