<?php
// src/Service/VerificationService.php

namespace App\Service;

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

class VerificationService
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

    public function generateToken(Utilisateur $user): VerificationToken
    {
        $token = new VerificationToken();
        $token->setUser($user)
              ->setToken(bin2hex(random_bytes(32)))
              ->setCreatedAt(new \DateTimeImmutable())
              ->setExpiresAt(new \DateTimeImmutable('+1 day'));
        $this->entityManager->persist($token);
        $this->entityManager->flush();

        return $token;
    }

    public function verifyToken(string $token): ?Utilisateur
    {
        $verificationToken = $this->entityManager
            ->getRepository(VerificationToken::class)
            ->findOneBy(['token' => $token]);

        if (!$verificationToken || $verificationToken->isExpired()) {
            return null;
        }

        $user = $verificationToken->getUser();
        $user->setStatus("Vérifié");

        $this->entityManager->remove($verificationToken);
        $this->entityManager->flush();

        return $user;
    }

    public function sendVerificationEmail(Utilisateur $user, string $verificationLink)
    {
        $verificationUrl = $this->router->generate('verify_email', ['token' => $verificationLink], UrlGeneratorInterface::ABSOLUTE_URL);
        $username = $user->getPrenom().' '.$user->getNom();
        $email = (new TemplatedEmail())
            ->from('assurancytn@gmail.com')
            ->to($user->getEmail())
            ->subject('Verify your email address')
            ->htmlTemplate("email/emailVerification.html.twig")
            ->context([
                'Username' => $username,
                'VerificationUrl' => $verificationUrl,
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
            ->getRepository(VerificationToken::class)
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
