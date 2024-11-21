<?php

namespace App\Controller;

use App\Entity\Immobilier;
use App\Entity\Utilisateur;
use App\Form\ImmobilierType;
use App\Repository\ImmobilierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/client/MyEstates')]
class ImmobilierController extends AbstractController
{
    #[Route('/', name: 'app_immobilier_index', methods: ['GET'])]
    public function index(Request $request,ImmobilierRepository $immobilierRepository): Response
    {
        $user_id = $request->getSession()->get('user_id');
        $sortBy = $request->query->get('sort_by'); // Récupérer le paramètre de tri
        $searchTerm = $request->query->get('q'); // Récupérer le terme de recherche
    
        // Si le paramètre de tri n'est pas défini, tri par défaut par ID
        if (!$sortBy || !in_array($sortBy, ['id_fiscal', 'superficie'])) {
            $sortBy = 'id_fiscal';
        }
    
        // Récupérer les données en fonction du tri souhaité et du terme de recherche
        $immobiliers = $immobilierRepository->findBySearchTerm($searchTerm, $sortBy);
    
        return $this->render('immobilier/index.html.twig', [
            'immobiliers' => $immobiliers,
            'user' => $user_id,
            'searchTerm' => $searchTerm, // Passer le terme de recherche au modèle Twig
        ]);
    }

    #[Route('/new', name: 'app_immobilier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $immobilier = new Immobilier();
        $form = $this->createForm(ImmobilierType::class, $immobilier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $immobilierId = $immobilier->getIdfiscal();
            $immobilierType = $immobilier->getType();
            $tempFilePath = $form['titre_prop']->getData();
            $nameph = $immobilierType.$immobilierId.".png";
            $destinationPath = "uploads/".$nameph;
            $compressionQuality = 100;
    
            $this->compressImage($tempFilePath, $destinationPath, $compressionQuality);
            
            $user_id = $request->getSession()->get('user_id');
            $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['id' => $user_id]);
            $immobilier->setIdUser($user);
            $immobilier->setTitreProp($nameph);
            $immobilier->setStatus("Non Validée");
            $entityManager->persist($immobilier);
            $entityManager->flush();

            return $this->redirectToRoute('app_immobilier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('immobilier/new.html.twig', [
            'immobilier' => $immobilier,
            'form' => $form,
        ]);
    }

    #[Route('/{id_fiscal}', name: 'app_immobilier_show', methods: ['GET'])]
    public function show(Immobilier $immobilier): Response
    {
        return $this->render('immobilier/show.html.twig', [
            'immobilier' => $immobilier,
        ]);
    }

    #[Route('/{id_fiscal}/edit', name: 'app_immobilier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Immobilier $immobilier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ImmobilierType::class, $immobilier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_immobilier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('immobilier/edit.html.twig', [
            'immobilier' => $immobilier,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id_fiscal}', name: 'app_immobilier_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Immobilier $immobilier, EntityManagerInterface $entityManager): Response
    {
            $immobilier->setIdUser(null);
            $entityManager->remove($immobilier);
            $entityManager->flush();

        return $this->redirectToRoute('app_immobilier_index', [], Response::HTTP_SEE_OTHER);
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