<?php
// src/Service/ResetPasswordService.php

namespace App\Service;

use App\Entity\ResetPasswordToken;
use App\Entity\Utilisateur;
use App\Entity\VerificationToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ResetPasswordService
{
    private $entityManager;
    private $mailer;
    private $router;
    

    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer,RouterInterface $router)
    {
        $this->entityManager = $entityManager;
        $transport = Transport::fromDsn($_ENV['MAILER_DSN']);
        $this->mailer = new Mailer($transport);
        $this->router = $router;
    }

    public function generateToken(Utilisateur $user): ResetPasswordToken
    {
        $token = new ResetPasswordToken();
        $token->setUser($user)
              ->setToken(bin2hex(random_bytes(32)))
              ->setRequestedAt(new \DateTimeImmutable())
              ->setExpiresAt(new \DateTimeImmutable('+1 hour'));
        $this->entityManager->persist($token);
        $this->entityManager->flush();

        return $token;
    }

    public function verifyToken(string $token): ?Utilisateur
    {
        $ResetPasswordToken = $this->entityManager
            ->getRepository(ResetPasswordToken::class)
            ->findOneBy(['token' => $token]);

        if (!$ResetPasswordToken || $ResetPasswordToken->isExpired()) {
            return null;
        }

        $user = $ResetPasswordToken->getUser();

        $this->entityManager->remove($ResetPasswordToken);
        $this->entityManager->flush();

        return $user;
    }

    public function sendResetPasswordEmail(Utilisateur $user, string $resetPasswordLink)
    {
        $resetPasswordUrl = $this->router->generate('Reset_Password_redirect', ['token' => $resetPasswordLink], UrlGeneratorInterface::ABSOLUTE_URL);
        $username = $user->getPrenom().' '.$user->getNom();
        $email = (new TemplatedEmail())
            ->from('assurancytn@gmail.com')
            ->to($user->getEmail())
            ->subject('Reset Password Confirmation - Action Required')
            ->htmlTemplate("email/emailReset.html.twig")
            ->context([
                'username' => $username,
                'resetPasswordUrl' =>$resetPasswordUrl,
            ]);
            $loader = new FilesystemLoader(__DIR__.'/../../templates');
            $twigEnv = new Environment($loader);
            $twigBodyRenderer = new BodyRenderer($twigEnv);
            $twigBodyRenderer->render($email);
        $this->mailer->send($email);
    }
    

public function cleanupExpiredTokens(): void
    {
        $expiredTokens = $this->entityManager
            ->getRepository(ResetPasswordToken::class)
            ->createQueryBuilder('vt')
            ->where('vt.expiresAt <= :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();

        foreach ($expiredTokens as $token) {
            $this->entityManager->remove($token);
        }

        $this->entityManager->flush();
    }
}
