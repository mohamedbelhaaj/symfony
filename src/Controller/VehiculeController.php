<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Vehicule;
use App\Form\VehiculeType;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/client/Myvehicules')]
class VehiculeController extends AbstractController
{
    #[Route('/', name: 'app_vehicule_index', methods: ['GET'])]
    public function index(Request $request,VehiculeRepository $vehiculeRepository): Response
    {
        $user_id = $request->getSession()->get('user_id');
        $sortBy = $request->query->get('sort_by'); // Récupérer le paramètre de tri
        $searchTerm = $request->query->get('q'); // Récupérer le terme de recherche
 
    // Si le paramètre de tri n'est pas défini, tri par défaut par matricule
    if (!$sortBy || !in_array($sortBy, ['matricule', 'prix'])) {
        $sortBy = 'matricule';
    }

    // Récupérer les données en fonction du tri souhaité
    $vehicules = $vehiculeRepository->findBySearchTerm($searchTerm, $sortBy);

    return $this->render('vehicule/index.html.twig', [
        'vehicules' => $vehicules,
        'user' => $user_id,
        'searchTerm' => $searchTerm, // Passer le terme de recherche au modèle Twig
    ]);
    }

    #[Route('/new', name: 'app_vehicule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $VehiculeId = $vehicule->getMatricule();
            $VehiculeMarque = $vehicule->getModele();
            $tempFilePath = $form['carte_grise']->getData();
            $nameph = $VehiculeMarque.$VehiculeId.".png";
            $destinationPath = "uploads/".$nameph;
            $compressionQuality = 100;
            
            $this->compressImage($tempFilePath, $destinationPath, $compressionQuality);
    
            $user_id = $request->getSession()->get('user_id');
            $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['id' => $user_id]);
            $vehicule->setIdUser($user);
            $vehicule->setCarteGrise($nameph);
            $vehicule->setStatus("Non Validée");
            $entityManager->persist($vehicule);
            $entityManager->flush();

            return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('vehicule/new.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form,
        ]);
    }

    #[Route('/{matricule}', name: 'app_vehicule_show', methods: ['GET'])]
    public function show(Vehicule $vehicule): Response
    {
        return $this->render('vehicule/show.html.twig', [
            'vehicule' => $vehicule,
        ]);
    }

    #[Route('/{matricule}/edit', name: 'app_vehicule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vehicule $vehicule, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('vehicule/edit.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{matricule}', name: 'app_vehicule_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Vehicule $vehicule, EntityManagerInterface $entityManager): Response
    {
        foreach ($vehicule->getDevis() as $devi) {
        foreach ($devi->getDevisItems() as $dev) {
            $entityManager->remove($dev);
        }
        $entityManager->remove($devi);
        }
            $vehicule->setIdUser(null);
            $entityManager->remove($vehicule);
            $entityManager->flush();

        return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
    }

    private  function compressImage($source, $destination, $quality) {
        $info = getimagesize($source);
        
        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source);
        } elseif ($info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($source);
        } else {
            return false;
        }    // Sauvegarder l'image compressée
        $filename = substr($destination, strpos($destination, '/') + 1);
        $destination2 = "C:/Users/hails/Pictures/Assurancy/Assurancy/src/main/resources/assets/uploads/".$filename;

        // Save the compressed image
        imagejpeg($image, $destination, $quality);
        imagejpeg($image, $destination2, $quality);
           
           // Libérer la mémoire
           imagedestroy($image);
           
           return true;
       }
    
}