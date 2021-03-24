<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

// Un authenticator est une classe qui va préciser à Symfony à quel moment elle a envie d'intervenir pour authentifier un utilisateur.
// C'est en général quand un utilisateur le demande
// Celui ci en l'occurrence n'interviendra que lorsqu'un utilisateur soumettra le formulaire de connexion (celui qui se trouve sur la route 'security_login')
// Une fois l'authenticator appelé, il va nous demander parmi toutes les données qu'il reçoit de l'objet Request, de récupérer les informations dont il a besoin.
// Ces données sont ensuite comparées aux données stockées dans la base de données.
// Si tout correspond, l'authenticator passe la méthode 'onAuthenticationSuccess', l'utilisateur est donc connecté !
// S'il y a le moindre soucis durant la procédure d'authentification, l'authenticator peut renvoyer une 'AuthenticationException' etSymfony nous renverra vers la méthode 'onAuthenticationFailure' en passant la request ET l'exception.

class LoginFormAuthenticator extends AbstractGuardAuthenticator
{
    // On se fait livrer le 'userPasswordEncoderInterface' car le mot de passe stocké en BDD est encodé et ne peut donc pas être comparé dans l'état par la méthode 'checkCredentials'
    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        // On stocke ici l'encoder que le container va nous passer dans notre propriété protégée 'encoder', ce qui va nous permettre d'y avoir accès dans TOUTES nos méthodes !
        $this->encoder = $encoder;
    }

    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'security_login' && $request->isMethod('POST');
    }

    // Une fois la méthode supports passée, et renvoie 'true', le formulaire a bien été soumis, les méthodes suivantes peuvent commencer à passer.
    // La méthode 'getCredentials' récupère les données du formulaire dans le tableau 'login'
    public function getCredentials(Request $request)
    {
        $request->attributes->set(Security::LAST_USERNAME,$request->request->get('login')['email']);

        return $request->request->get('login');
        // Le tableau 'login' retourné ici contient 3 informations :
        // 1- l'email de l'utilisateur "email"
        // 2- le mot de passe de l'utilisateur "password"
        // 3- le token CSRF "_token"
    }

    // Une fois les informations récupérées dans le formulaire, il va tenter d'aller chercher l'utilisateur dans la base de données.
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            return $userProvider->loadUserByUsername($credentials['email']);
        } catch (UsernameNotFoundException $e) {
            throw new AuthenticationException("Cette adresse email n'est pas connue.");
        }

        // Ici, grace au "security.yaml", l'userProvider va aller chercher dans l'entité User, celui dont l'email correspond !
    }

    // Une fois les données de l'user récupérées, celles ci sont renvoyées à la méthode 'checkCredentials' afin d'être vérifiées.
    public function checkCredentials($credentials, UserInterface $user)
    {
        // Cette méthode reçoit alors les informations de connexion ET l'utilisateur trouvé par la méthode 'getUser'
        // Les données sont alors comparées à celles présentes dans la BDD
        // Vérifier que le mot de passe fourni corresponde bien à celui stocké en base de données (qui est encodé)
        // $credentials['password'] => $user->getPassword()
        // La fonction 'isPasswordValid' prend deux paramètres :
        // 1 - l'utilisateur dont on veut vérifier le mot de passe
        // 2 - le mot de passe à vérifier

        $isValid = $this->encoder->isPasswordValid($user, $credentials['password']);

        if (!$isValid) {
            throw new AuthenticationException("Les informations saisies ne correspondent pas.");
        }

        return true;
    }

    // Cette fonction n'est appelée QUE lorsque la procédure d'authentification n'a PAS réussi !
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // Ici, la réponse à la méthode 'checkCredentials' est FALSE, je ne suis PAS authentifié !
        $request->attributes->set(Security::AUTHENTICATION_ERROR, $exception);
    }

    // Cette fonction n'est appelée QUE lorsque la procédure d'authentification a réussi !
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        // Ici, la réponse à la méthode 'checkCredentials' est TRUE, je suis authentifié !
        // L'utilisateur est alors redirigé vers la page d'accueil.
        return new RedirectResponse('/');
    }

    // Cette méthode est appelée, lorsqu'un utilisateur ANONYME envoie une requête pour accéder à une ressource qui nécessite qu'il soit connecté.
    public function start(Request $request, AuthenticationException $authException = null)
    {
    	// Ici l'utilisateur essaie d'accéder à la partie admin, on le redirige vers la page de connexion.
        return new RedirectResponse('/login');
    }

    public function supportsRememberMe()
    {
        // todo
    }
}
