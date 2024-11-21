<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ResetPasswordType;
use App\Form\UtilisateurIType;
use App\Form\UtilisateurLType;
use App\Form\UtilisateurPType;
use App\Repository\MechanoRepository;
use App\Repository\PublicationRepository;
use App\Security\AppAuthenticator;
use App\Service\VerificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route('/client')]
class ServiceController extends AbstractController
{
    #[Route('/accueil', name: 'index')]
    public function index(SessionInterface $session): Response
    {
        return $this->render('service/index.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }

    #[Route('/about', name: 'about')]
    public function about(SessionInterface $session): Response
    {
        if ($session->has('user_id')) {
        return $this->render('service/about.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }
    else {
        return $this->redirectToRoute('login');
    }
    }

    #[Route('/offres', name: 'offres')]
    public function offres(SessionInterface $session): Response
    {
        if ($session->has('user_id')) {
        return $this->render('service/offres.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }
    else {
        return $this->redirectToRoute('login');
    }
    }

    #[Route('/assistance', name: 'assistance')]
    public function assistance(SessionInterface $session): Response
    {
        if ($session->has('user_id')) {
        return $this->render('service/assistance.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }
    else {
        return $this->redirectToRoute('login');
    }
    }

    #[Route('/services', name: 'services')]
    public function services(MechanoRepository $mechanoRepository): Response
    {
        return $this->render('service/services.html.twig', [
            'controller_name' => 'ServiceController',
            'mechanos' => $mechanoRepository->findAll(),
        ]);
    }

    #[Route('/community', name: 'community')]
    public function community(PublicationRepository $publicationRepository): Response
    {
        // Retrieve publications from the repository
        $publications = $publicationRepository->findAll();
        
        // Render the template and pass the publications variable
        return $this->render('service/community.html.twig', [
            'publications' => $publications,
        ]);
    }

    #[Route('/Ajouter-un-bien', name: 'ajouter_un_bien')]
    public function ajouter_un_bien(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user_id = $request->getSession()->get('user_id');
        $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['id' => $user_id]);
        return $this->render('service/ajouter_un_bien.html.twig', [
            'controller_name' => 'ServiceController',
            'user' => $user,
        ]);
    }

    #[Route('/profile/{id}', name: 'profile', methods: ['GET', 'POST'])]
    public function profile(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager, SessionInterface $session, UserPasswordEncoderInterface $passwordEncoder): Response
{
    if ($session->has('user_id')) {
    $userId = $request->getSession()->get('user_id');

    if ($userId !== $utilisateur->getId()) {
        return $this->redirectToRoute('index');
    }
    $formRs = $this->createForm(ResetPasswordType::class);
    $formRs->handleRequest($request);
    $form = $this->createForm(UtilisateurPType::class, $utilisateur);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $tempFilePath = $form['image']->getData();
          if ($tempFilePath){
            if ($utilisateur->getImage()) {
                unlink($utilisateur->getImage());
            }
            $nameph = "User". md5(uniqid(rand(), true)).$utilisateur->getId().".png";
          $destinationPath = "uploads/".$nameph;
          $compressionQuality = 100;
  
          $this->compressImage($tempFilePath, $destinationPath, $compressionQuality);
  
            $utilisateur->setImage($nameph);
        }
        $entityManager->flush();
        $request->getSession()->set('user_image', $utilisateur->getImage());
        $this->addFlash('success', "Modification enregistré avec succés");
    }
    if ($formRs->isSubmitted() && $formRs->isValid()) {
        $currentPassword = $formRs->get('cup')->getData();
        $newPassword = $formRs->get('np')->getData();
        if (!$passwordEncoder->isPasswordValid($utilisateur, $currentPassword)) {
            $this->addFlash('error', 'Incorrect current password.');
            return $this->redirectToRoute('profile',['id' => $utilisateur->getId()]);
        }
        $hashedPassword = $passwordEncoder->encodePassword($utilisateur, $newPassword);
        $utilisateur->setMdp($hashedPassword);
        $entityManager->flush();
        $this->addFlash('success', "Password has been updated successfully!");
    }
    return $this->renderForm('service/profile.html.twig', [
        'utilisateur' => $utilisateur,
        'form' => $form,
        'formRs' => $formRs,
        'reclamations' => $utilisateur->getReclamations(),
        'rdvs' => $utilisateur->getRDV(),
        'demandes' => $utilisateur->getDemandes(),
        'constats' => $utilisateur->getConstats(),
        'devis' => $utilisateur->getDevis(),
        'assurances' => $utilisateur->getAssurances(),
    ]);
    }
    else {
        return $this->redirectToRoute('login');
    }
}

private function compressImage($source, $destination, $quality) {
    // Check if the image already exists at the destination path
    if (file_exists($destination)) {
        // Delete the existing image
        if (!unlink($destination)) {
            // Failed to delete the existing image
            return false;
        }
    }

    $info = getimagesize($source);

    // Determine the MIME type of the image and create an image resource
    if ($info['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($source);
    } elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($source);
    } elseif ($info['mime'] == 'image/gif') {
        $image = imagecreatefromgif($source);
    } else {
        return false; // Unsupported image type
    }

    // Save the compressed image
    $filename = substr($destination, strpos($destination, '/') + 1);
        $destination2 = "C:/Users/hails/Pictures/Assurancy/Assurancy/src/main/resources/assets/uploads/".$filename;
        
        // Save the compressed image
        imagejpeg($image, $destination, $quality);
        imagejpeg($image, $destination2, $quality);

    // Free up memory by destroying the image resource
    imagedestroy($image);

    return true; // Compression successful
}
}