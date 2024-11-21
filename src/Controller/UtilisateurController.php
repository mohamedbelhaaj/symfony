<?php

namespace App\Controller;

use App\Entity\Assurance;
use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Form\UtilisateurEType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Enum\typeUser;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('admin/utilisateurs')]
class UtilisateurController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }
    private function getSiteData()
    {
        $NBClients = count($this->entityManager->getRepository(Utilisateur::class)->findBy(['role' => 'Client']));
        $NBSOS = count($this->entityManager->getRepository(Utilisateur::class)->findBy(['role' => 'Agent SOS'])); 
        $NBExperts = count($this->entityManager->getRepository(Utilisateur::class)->findBy(['role' => 'Expert']));  
        $NBEnsurances = count($this->entityManager->getRepository(Assurance::class)->findAll());
        return [$NBClients,$NBSOS,$NBExperts,$NBEnsurances];
    }

    #[Route('/', name: 'app_utilisateurs', methods: ['GET'])]
    public function index(UtilisateurRepository $utilisateurRepository, SessionInterface $session): Response
    {
        if ($session->has('user_id') && $session->has('user_role')) {
            $userRole = $session->get('user_role');
            $data = $this->getSiteData();
            if ($userRole == "Admin") {
        return $this->render('dashboard/users.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll(),
            'NBClients' => $data[0],
            'NBSOS' => $data[1],
            'NBExperts' => $data[2],
            'NBEnsurances' => $data[3],
        ]);
            }
            else {
                return $this->redirectToRoute('app_dashboard');
                }
        }
        else {
            return $this->redirectToRoute('firstofall1'); 
        }
    }



    #[Route('/new', name: 'app_utilisateur_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
{
    if ($session->has('user_id') && $session->has('user_role')) {
        $userRole = $session->get('user_role');
        if ($userRole == "Admin") {
    $utilisateur = new Utilisateur();
    $utilisateur->setDateC(new \DateTime());
    $utilisateur->setDateN(new \DateTime('01-01-2000'));
    $form = $this->createForm(UtilisateurType::class, $utilisateur);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Check for existing email (case-insensitive comparison)
        $existingUser = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => strtolower($utilisateur->getEmail())]);
        if ($existingUser) {
            $this->addFlash('error', 'Email already exists.');
            return $this->redirectToRoute('app_utilisateur_new');
        }

        // Set date and status
        $utilisateur->setDateC(new \DateTime());
        $utilisateur->setStatus("Non vérifié");

        $hashedPassword = password_hash($utilisateur->getMdp(), PASSWORD_DEFAULT);
        $utilisateur->setMdp($hashedPassword);

        // Persist user
        $entityManager->persist($utilisateur);
        $entityManager->flush();

        // Redirect to user list page
        return $this->redirectToRoute('app_utilisateurs', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('utilisateur/new.html.twig', [
        'utilisateur' => $utilisateur,
        'form' => $form,
    ]);
}
else {
    return $this->redirectToRoute('app_dashboard');
    }
}
else {
return $this->redirectToRoute('firstofall1'); 
}
}

    #[Route('/{id}', name: 'app_utilisateur_show', methods: ['GET'])]
    public function show(Utilisateur $utilisateur, SessionInterface $session): Response
    {
        if ($session->has('user_id') && $session->has('user_role')) {
            $userRole = $session->get('user_role');
            if ($userRole == "Admin") {
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }
    else {
        return $this->redirectToRoute('app_dashboard');
        }
}
else {
    return $this->redirectToRoute('firstofall1'); 
}
    }

    #[Route('/edit/{id}', name: 'app_utilisateur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        if ($session->has('user_id') && $session->has('user_role')) {
            $userRole = $session->get('user_role');
            if ($userRole == "Admin") {
        $form = $this->createForm(UtilisateurEType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_utilisateurs', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }
    else {
        return $this->redirectToRoute('app_dashboard');
        }
}
else {
    return $this->redirectToRoute('firstofall1'); 
}
    }

    #[Route('/{id}/verify', name: 'app_utilisateur_verify', methods: ['GET', 'POST'])]
    public function verify(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        if ($session->has('user_id') && $session->has('user_role')) {
            $userRole = $session->get('user_role');
            if ($userRole == "Admin") {
            if ($utilisateur->getStatus() == "Non vérifié"){
                $utilisateur->setStatus("Vérifié");
                $entityManager->flush();
            }
            else{
                $utilisateur->setStatus("Non vérifié");
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_utilisateurs', [], Response::HTTP_SEE_OTHER);
        }
        else {
            return $this->redirectToRoute('app_dashboard');
            }
    }
    else {
        return $this->redirectToRoute('firstofall1'); 
    }
    }
}
