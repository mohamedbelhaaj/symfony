<?php

namespace App\Controller;

use App\Entity\Assurance;
use App\Entity\Vehicule;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AssuranceType;
use App\Repository\AssuranceRepository;
use App\Repository\ImmobilierRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\VehiculeRepository;
use App\Service\InfobipService;
use DateTime;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AssuranceController extends AbstractController
{

    #[Route('/client/assuranceadd', name: 'add_assurance')]
    public function addAssurance(ManagerRegistry $man, Request $request, VehiculeRepository $vehrep, ImmobilierRepository $immobrep,UtilisateurRepository $utirep): Response
    {
        $idUtilisateur = $request->getSession()->get('user_id'); // remplacée
        $em = $man->getManager();
        $assurance = new Assurance();

        $form = $this->createForm(AssuranceType::class, $assurance, [
            'vehicules' => $vehrep->vehiclesListByUsers($idUtilisateur),
            'immobs' => $immobrep->immobsListByUsers($idUtilisateur),
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user_id = $request->getSession()->get('user_id');
            $utilisateur = $utirep->find($user_id);  // remplacée
            $assurance->setIdUser($utilisateur);
            $em->persist($assurance);
            $em->flush();

            return $this->redirectToRoute('add_assurance');
        }

        return $this->render('assurance/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/admin/assurances/delete_assurance/{id}', name: 'assurance_delete')]
    public function deleteAssurance($id, ManagerRegistry $manager, AssuranceRepository $AssuranceRepository): Response
    {
        $em = $manager->getManager();
        $assurance = $AssuranceRepository->find($id);
        $em->remove($assurance);
        $em->flush();
        return $this->redirectToRoute('app_assurances');
    }



    #[Route('/admin/assurances/modifier_assurance/{id}', name: 'assurance_edit')]
    public function editAssurance(Request $request, ManagerRegistry $manager, $id, AssuranceRepository $AssuranceRepository,VehiculeRepository $vehrep, ImmobilierRepository $immobrep): Response
    {
        $em = $manager->getManager();
        $assurance = $AssuranceRepository->find($id);
        // $user = $assurance->getIdUser();
        // $id_user = $user->getId(); 
        // $form = $this->createForm(AssuranceType::class, $assurance, [
        //     'vehicules' => $vehrep->vehiclesListByUsers($id_user),
        //     'immobs' => $immobrep->immobsListByUsers($id_user),
        // ]);
        $formBuilder = $this->createFormBuilder($assurance)
            ->add('date_d')
            ->add('date_f')
            ->add('prix')
            ->add('save', SubmitType::class, ['label' => 'Enregistrer Assurance']);
    
        $form = $formBuilder->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($assurance);
            $em->flush();
            return $this->redirectToRoute('app_assurances');
        }
    
        return $this->renderForm('assurance/edit.html.twig', [
            'assurance' => $assurance,
            'form' => $form,
        ]);
    }
   // #[Route('/sms/{id}/{telass}',name: 'send_sms')]
   #[Route('/sms/{id}/{id2}',name: 'send_sms')]
   public function sendSms(InfobipService $infobipService,$id,UtilisateurRepository $user,$id2)
   {
       $infouser = $user->find($id);
       $phoneNumber = '+216'.strval($infouser->getTel());
       $nom = $infouser->getNom(); 
       $message = 'Bonjour cher client '.$nom.' '.",Votre contrat d'assurance ID ".$id2." a été expiré, veuillez régler votre situation chez Assurancy.tn";
   
       $result = $infobipService->sendSms($phoneNumber, $message);
   
       return $this->redirectToRoute('app_assurances');
   }
   

   #[Route('/ntftout',name: 'ntftout')]
   public function notifExpiration(AssuranceRepository $assrep,InfobipService $infobipService){
       $assurances = $assrep->findAssurancesNotif();
       foreach ($assurances as $assurance) {
           $number= '+216'.strval($assurance->getIdUser()->getTel());
           $nom = $assurance->getIdUser()->getNom();
           $idass = $assurance->getId();
           $message = 'Bonjour cher client '.$nom.' '.",Votre contrat d'assurance ID ".$idass." a été expiré, veuillez régler votre situation chez Assurancy.tn";
           $result = $infobipService->sendSms($number, $message);
       }
       return $this->redirectToRoute('app_assurances');
   }



   #[Route('/prolonger/{id}',name: 'prolonger')]
   
   public function Prolonger($id,AssuranceRepository $AssuranceRepository,ManagerRegistry $manager)
   {
       $em = $manager->getManager();
       $assurance = $AssuranceRepository->find($id);
       $newDate = $assurance->getDateF();
       $originalDateTime = DateTime::createFromInterface($newDate);
       $modifiedDateTime = $originalDateTime->modify('+1 month');
       $assurance->setDateF($modifiedDateTime);
       $em->persist($assurance);
       $em->flush();
       return $this->redirectToRoute('app_assurances');
   
   }


   #[Route(name: 'remise')]
   
   public function remise(AssuranceRepository $AssuranceRepository,ManagerRegistry $manager)
   {
       $em = $manager->getManager();
       $assurances = $AssuranceRepository->findall();
       foreach ($assurances as $assurance){
           $assurance->setPrix( $assurance->getPrix() - ($assurance->getPrix()/100) * 10 );
           $em->persist($assurance);
       }
       $em->flush();
       return $this->redirectToRoute('app_assurances');
   
   }

}
