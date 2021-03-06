<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 18.05.2016
 * Time: 23:38
 */

namespace AppBundle\Security;


use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Guard\AuthenticatorInterface;

class LegacyCookieAuthenticator extends AbstractGuardAuthenticator implements AuthenticatorInterface
{
    /**
     * @var ObjectManager
     */
    private $em;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ObjectManager $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function getCredentials(Request $request)
    {
        $cookie = $request->cookies->get("KaroKeks");
        //$this->logger->debug("KEKS " . $cookie);
        if (!$cookie) {
            throw new AuthenticationException('No Keks');
        }
        if (!($codestring = base64_decode($cookie))) {
            throw new AuthenticationException('No base im Keks');
        }
        list($id, $hash) = explode('|--|', $codestring);
        //$this->logger->debug($id . ":" . $hash);
        if ($id) {
            return array(
                "id" => $id,
                "hash" => $hash,
            );
        }

        throw new AuthenticationException('Nix good Keks');
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $this->user = $this->em->find('AppBundle:User', $credentials['id']);

        //$this->logger->debug("Found user " . $this->user);
        return $this->user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        //$this->logger->debug("CHekc user " . $user);
        //$this->logger->debug("COmpare " . $credentials);
        return ((md5($this->user->getPassword())) == $credentials['hash']);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $this->logger->debug("Auth fail from cookie");
        // TODO: Implement onAuthenticationFailure() method.
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $this->logger->debug("Auth success from cookie");
        // TODO: Implement onAuthenticationSuccess() method.
    }

    public function supportsRememberMe()
    {
        return false;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        // TODO: Implement start() method.
    }

    public function supports(Request $request)
    {
        return true;
    }

}
