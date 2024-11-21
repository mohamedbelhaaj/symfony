<?php

namespace App\Controller;

use App\Entity\Evaluation;
use App\Entity\Immobilier;
use App\Entity\Utilisateur;
use App\Entity\Vehicule;
use App\Form\EvaluationType;
use App\Repository\EvaluationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/evaluation')]
class EvaluationController extends AbstractController
{
    #[Route('/', name: 'app_evaluation_index', methods: ['GET'])]
    public function index(EvaluationRepository $evaluationRepository): Response
    {
        return $this->render('evaluation/index.html.twig', [
            'evaluations' => $evaluationRepository->findAll(),
        ]);
    }

    #[Route('/voiture/new/{matricule}', name: 'app_evaluation_newV', methods: ['GET', 'POST'])]
    public function newV(Request $request, Vehicule $veh, EntityManagerInterface $entityManager): Response
    {
        $evaluation = new Evaluation();
        $form = $this->createForm(EvaluationType::class, $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user_id = $request->getSession()->get('user_id');
            $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['id' => $user_id]);
            $evaluation->setIdExpert($user);
            $evaluation->setDate(new \DateTime());
            $veh->setPrix($evaluation->getValeurvenal());
            $veh->setEvaluation($evaluation);
            $veh->setStatus("Validée");
            $entityManager->persist($evaluation);
            $entityManager->flush();

            return $this->redirectToRoute('app_evaluation', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evaluation/new.html.twig', [
            'evaluation' => $evaluation,
            'form' => $form,
        ]);
    }

    #[Route('/immobilier/new/{id_fiscal}', name: 'app_evaluation_newI', methods: ['GET', 'POST'])]
    public function newI(Request $request, Immobilier $imm, EntityManagerInterface $entityManager): Response
    {
        $evaluation = new Evaluation();
        $form = $this->createForm(EvaluationType::class, $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user_id = $request->getSession()->get('user_id');
            $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['id' => $user_id]);
            $evaluation->setIdExpert($user);
            $evaluation->setDate(new \DateTime());
            $imm->setEvaluation($evaluation);
            $imm->setStatus("Validée");
            $entityManager->persist($evaluation);
            $entityManager->flush();

            return $this->redirectToRoute('app_evaluation', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evaluation/new.html.twig', [
            'evaluation' => $evaluation,
            'form' => $form,
        ]);
    }

    #[Route('/showV/{matricule}', name: 'app_evaluation_showV', methods: ['GET'])]
    public function showV(Vehicule $veh): Response
    {
        return $this->render('evaluation/show.html.twig', [
            'veh' => $veh,
            'imm' => null,
        ]);
    }

    #[Route('/showI/{id_fiscal}', name: 'app_evaluation_showI', methods: ['GET'])]
    public function showI(Immobilier $imm): Response
    {
        return $this->render('evaluation/show.html.twig', [
            'imm' => $imm,
            'veh' => null,
        ]);
    }

    #[Route('/deleteV/{matricule}', name: 'app_evaluation_deleteV', methods: ['GET','POST'])]
    public function deleteV(Request $request, Vehicule $veh, EntityManagerInterface $entityManager): Response
    {
            $evaluation = $veh->getEvaluation();
            $evaluation->setIdExpert(null);
            $entityManager->remove($evaluation);
            $veh->setEvaluation(null);
            $veh->setStatus("Non Validée");
            $entityManager->flush();

        return $this->redirectToRoute('app_evaluation', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/deleteI/{id_fiscal}', name: 'app_evaluation_deleteI', methods: ['GET','POST'])]
    public function deleteI(Request $request, Immobilier $imm, EntityManagerInterface $entityManager): Response
    {
        $evaluation = $imm->getEvaluation();
        $evaluation->setIdExpert(null);
        $entityManager->remove($evaluation);
        $imm->setEvaluation(null);
        $imm->setStatus("Non Validée");
        $entityManager->flush();

        return $this->redirectToRoute('app_evaluation', [], Response::HTTP_SEE_OTHER);
    }
}
