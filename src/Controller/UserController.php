<?php

namespace App\Controller;

use App\Entity\ResetPasswordToken;
use App\Entity\Utilisateur;
use App\Form\UtilisateurIType;
use App\Security\AppAuthenticator;
use App\Service\ResetPasswordService;
use App\Service\VerificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserController extends AbstractController
{
    #[Route('/', name: 'firstofall')]
    public function firstofall(): Response
    {
        return $this->redirectToRoute('login');
    }
    
    #[Route('/login', name: 'login')]
public function login(Request $request,AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager, Security $security): Response
{       
    $currentUser = $security->getUser();
    if ($currentUser){
        return $this->redirectToRoute('index');
    }else {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        if ($error !== null) {
            $errorMessage = $error->getMessage();
        } else {
            $errorMessage = null;
        }
    }

    return $this->render('service/login.html.twig', [
        'error' => $errorMessage,
        'last_username' => $lastUsername,
    ]);
}

#[Route('/logout', name: 'logout')]
    public function logout(SessionInterface $session) : Response
    {
        if ($session->has('user_id') && $session->has('user_role')) {
            $session->remove('user_id');
            $session->remove('user_role');
            $session->remove('user_name');
            $session->remove('user_image');
        }
        return $this->redirectToRoute('login');
    }

    #[Route('/inscription', name: 'inscription')]
public function inscription(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, VerificationService $verificationService, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator): Response
{
    $utilisateur = new Utilisateur();
    $utilisateur->setDateC(new \DateTime());
    $utilisateur->setDateN(new \DateTime('01-01-2000'));
    $utilisateur->setStatus("Non vérifié");
    $utilisateur->setRole("Client");
    $utilisateur->setImage("null");

    $form = $this->createForm(UtilisateurIType::class, $utilisateur);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $utilisateur->getEmail()]);
        if ($user) {
            toastr()->addWarning('This e-mail already exists !');
        }
        else {
        $utilisateur->setMdp(
            $userPasswordHasher->hashPassword(
                $utilisateur,
                $form->get('mdp')->getData()
            )
        );
        $entityManager->persist($utilisateur);
        $entityManager->flush();

        $token = $verificationService->generateToken($utilisateur);
        $verificationService->sendVerificationEmail($utilisateur, $token->getToken());

        return $this->redirectToRoute('verify_email_page', ['id' => $utilisateur->getId()]);
        }
    }

    return $this->renderForm('service/inscription.html.twig', [
        'utilisateur' => $utilisateur,
        'form' => $form,
    ]);
}

#[Route('/verify-email/{token}', name: 'verify_email')]
public function verifyEmail(string $token, VerificationService $verificationService): Response
{
    $user = $verificationService->verifyToken($token);

    if ($user) {
        toastr()->addSuccess('Email verified successfully !');
        return $this->redirectToRoute('login');
    } else {
        toastr()->addWarning('Invalid or expired token !');
        return $this->redirectToRoute('login');
    }
}

#[Route('/ResetRequest', name: 'reset_Request')]
public function resetRequest(Request $request, EntityManagerInterface $entityManager, ResetPasswordService $resetPasswordService): Response
{
    $form = $this->createFormBuilder()
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Please enter an email address.']),
                    new Email(['message' => 'The email "{{ value }}" is not a valid email.']),
                ],
            ])
            ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $email = $form->getData()['email'];
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
        if ($utilisateur) {
            $token = $resetPasswordService->generateToken($utilisateur);
            $resetPasswordService->sendResetPasswordEmail($utilisateur, $token->getToken());
            $request->getSession()->set('GET_BACK_FROM_RESET', "login");
            toastr()->addSuccess('A confirmation has been sent to your E-mail !');
        }
        else {
            toastr()->addError('This E-mail does not exist !');
        }
    }
    return $this->render('resetpass/EmailCheck.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/AdminResetRequest', name: 'reset_RequestA')]
public function resetRequestA(Request $request, EntityManagerInterface $entityManager, ResetPasswordService $resetPasswordService): Response
{
    $form = $this->createFormBuilder()
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Please enter an email address.']),
                    new Email(['message' => 'The email "{{ value }}" is not a valid email.']),
                ],
            ])
            ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $email = $form->getData()['email'];
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
        if ($utilisateur) {
            $token = $resetPasswordService->generateToken($utilisateur);
            $resetPasswordService->sendResetPasswordEmail($utilisateur, $token->getToken());
            $request->getSession()->set('GET_BACK_FROM_RESET', "login_d");
            toastr()->addSuccess('A confirmation has been sent to your E-mail !');
        }
        else {
            toastr()->addError('This E-mail does not exist !');
        }
    }
    return $this->render('resetpass/EmailCheck.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/ResetPasswordRedirect/{token}', name: 'Reset_Password_redirect')]
public function ResetPasswordRedirect(string $token, ResetPasswordService $resetPasswordService): Response
{
    $user = $resetPasswordService->verifyToken($token);

    if ($user) {
        return $this->redirectToRoute("Reset_Password",['id' => $user->getId()]);
    } else {
        toastr()->addError('Invalid or expired token !');
        return $this->redirectToRoute('login');
    }
}

#[Route('/ResetPassword/{id}', name: 'Reset_Password')]
public function ResetPassword(Utilisateur $user, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
{
    $form = $this->createFormBuilder()
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'required' => true,
                'first_options'  => [
                    'label' => 'New Password',
                    'attr' => ['class' => 'form-control p-input'],
                    'constraints' => [
                        new NotBlank([
                            'message' => "Confirm password is required.",
                        ]),
                        new Regex([
                            'pattern' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                            'message' => "Le mot de passe doit être composé de 8 caractères, contenant au minimum une majuscule, une minuscule, un chiffre et un caractère spécial.",
                        ]),
                    ]
                ],
                'second_options' => [
                    'label' => 'Repeat New Password',
                    'attr' => ['class' => 'form-control p-input'],
                    'constraints' => [
                        new NotBlank([
                            'message' => "Confirm password is required.",
                        ]),
                        new Regex([
                            'pattern' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                            'message' => "Le mot de passe doit être composé de 8 caractères, contenant au minimum une majuscule, une minuscule, un chiffre et un caractère spécial.",
                        ]),
                    ]
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $newPassword = $formData['password'];
            $user->setMdp(
                $userPasswordHasher->hashPassword(
                    $user,
                    $newPassword
                )
            );
            $entityManager->flush();
            toastr()->addSuccess('Your password has been changed successfully !');
            $path = $request->getSession()->get("GET_BACK_FROM_RESET");
            return $this->redirectToRoute($path);
        }

        return $this->render('resetpass/ResetPassword.html.twig', [
            'form' => $form->createView(),
        ]);
}

/*
#[Route('/Adminlogin', name: 'login_d')]
public function loginA(AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager): Response
{
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        if ($error !== null) {
            $errorMessage = $error->getMessage();
        } else {
            $errorMessage = null;
        }

    return $this->render('dashboard/login.html.twig', [
        'error' => $errorMessage,
        'last_username' => $lastUsername,
    ]);
}

#[Route('/Adminlogout', name: 'logout_d')]
    public function logoutA(AuthenticationUtils $authenticationUtils,SessionInterface $session) : Response
    {
        if ($session->has('user_id') && $session->has('user_role')) {
            $session->remove('user_id');
            $session->remove('user_role');
            $session->remove('user_name');
            $session->remove('user_image');
        }
        $session->migrate();
        $authenticationUtils->getLastAuthenticationError();
        $this->get('security.token_storage')->setToken(null);
        $session->invalidate();
        return $this->redirectToRoute('login_d');
    }
*/
    #[Route('/Verify-Your-Email/{id}', name: 'verify_email_page', methods:['GET'])]
public function EmailVerify(Utilisateur $user): Response
{
    return $this->render('a_verifpages/emailsent.html.twig', [
        'user' => $user,
    ]);
}

#[Route('/ResendVerification/{id}', name: 'resendVerification', methods:['GET'])]
public function ResendVerification(Utilisateur $utilisateur, VerificationService $verificationService): Response
{
    $token = $verificationService->generateToken($utilisateur);
    $verificationService->sendVerificationEmail($utilisateur, $token->getToken());
    return $this->redirectToRoute('verify_email_page',['id' => $utilisateur->getId()]);
}
}
