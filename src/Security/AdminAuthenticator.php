<?php

namespace App\Security;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AdminAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    private $entityManager = null;

    public function __construct(private UrlGeneratorInterface $urlGenerator, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function authenticate(Request $request): Passport
    {
        $path = $request->attributes->get('_route');
        $email = $request->request->get('email', '');
        $request->getSession()->set(Security::LAST_USERNAME, $email);
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
        if (!$user) {
            throw new CustomUserMessageAuthenticationException("This e-mail doesn't exist.");
        }
        if ($user->getStatus() !== 'Vérifié') {
            throw new CustomUserMessageAuthenticationException('Your account is not verified, an admin need to verify you.');
        }
        if ($user->getRole() != 'Client') {
                return new Passport(
                    new UserBadge($email),
                    new PasswordCredentials($request->request->get('password', '')),
                    [
                        new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                        new RememberMeBadge(),
                    ]
                );
        }
        throw new CustomUserMessageAuthenticationException("You don't have access here.");
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): Response
    {
        $user = $token->getUser();
        if ($user instanceof Utilisateur) {
        $request->getSession()->set('user_id', $user->getId());
        $request->getSession()->set('user_name', $user->getPrenom() . ' ' . $user->getNom());
        $request->getSession()->set('user_role', $user->getRole());
        $request->getSession()->set('user_image', $user->getImage());
        }
            if ($user instanceof Utilisateur && $user->isAdmin()) {
                return new RedirectResponse($this->urlGenerator->generate('app_dashboard'));
            } elseif ($user instanceof Utilisateur && $user->isExpert()) {
                return new RedirectResponse($this->urlGenerator->generate('app_dashboard'));
            } elseif ($user instanceof Utilisateur && $user->isSOS()) {
                return new RedirectResponse($this->urlGenerator->generate('app_dashboard'));
            }
            else {
                return new RedirectResponse($this->urlGenerator->generate('logout'));
            }
    }
    
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('login_d');
    }
}
