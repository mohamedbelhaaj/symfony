<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\Vehicule;
use App\Form\DevisType;
use App\Repository\DevisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/devis')]
class DevisController extends AbstractController
{
    #[Route('/', name: 'app_devis_index', methods: ['GET'])]
    public function index(DevisRepository $devisRepository): Response
    {
        return $this->render('devis/index.html.twig', [
            'devis' => $devisRepository->findAll(),
        ]);
    }

    #[Route('/list/{matricule}', name: 'app_devis_list', methods: ['GET'])]
    public function list(Vehicule $veh): Response
    {
        return $this->render('expert/listdevis.html.twig', [
            'vehicule' => $veh,
        ]);
    }

    #[Route('/Itemslist/{id}', name: 'app_devisItems_list', methods: ['GET'])]
    public function listItems(Devis $devi): Response
    {
        return $this->render('expert/listdevisItems.html.twig', [
            'devis' => $devi,
        ]);
    }

    #[Route('/new/{matricule}', name: 'app_devis_new', methods: ['GET', 'POST'])]
    public function new(Request $request,Vehicule $veh, EntityManagerInterface $entityManager): Response
    {
        $devi = new Devis();
        $form = $this->createForm(DevisType::class, $devi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $devi->setEtat("Non traité");
            $devi->setVehicule($veh);
            $devi->setTotalTTC(0);
            $devi->setDate(new \DateTime());
            $entityManager->persist($devi);
            $entityManager->flush();

            return $this->redirectToRoute('app_devis_list', ['matricule' => $veh->getMatricule()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('devis/new.html.twig', [
            'devi' => $devi,
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'app_devis_show', methods: ['GET'])]
    public function show(Devis $devi): Response
    {
        return $this->render('devis/show.html.twig', [
            'devi' => $devi,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_devis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Devis $devi, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DevisType::class, $devi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_devis_list', ['matricule' => $devi->getVehicule()->getMatricule()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('devis/edit.html.twig', [
            'devi' => $devi,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_devis_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Devis $devi, EntityManagerInterface $entityManager): Response
    {
        foreach ($devi->getDevisItems() as $dev) {
            $entityManager->remove($dev);
        }
        $entityManager->remove($devi);
        $entityManager->flush();

        return $this->redirectToRoute('app_devis_list', ['matricule' => $devi->getVehicule()->getMatricule()], Response::HTTP_SEE_OTHER);
    }
}
