<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 18.05.2016
 * Time: 23:38
 */

namespace AppBundle\Security;


use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class LegacyCookieAuthenticator extends AbstractGuardAuthenticator
{
    /** @var  LoggerInterface $logger */
    private $logger;
    private $em;
    /** @var  User */
    private $user;

    public function __construct(EntityManager $em, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->em = $em;
    }

    public function getCredentials(Request $request)
    {
        $cookie = $request->cookies->get("KaroKeks");
        if (!$cookie) return null;
        if (!($codestring = base64_decode($cookie))) return null;
        list($id, $hash) = explode('|--|', $codestring);
        if ($id) {
            return array(
                    "id" => $id,
                    "hash" => $hash
            );
        }
        return null;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $this->user = $this->em->find('AppBundle:User', $credentials['id']);
        return $this->user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return ((md5($this->user->getPassword())) == $credentials['hash']);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // TODO: Implement onAuthenticationFailure() method.
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
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


}