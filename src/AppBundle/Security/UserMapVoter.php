<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 21.06.2016
 * Time: 01:00
 */

namespace AppBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use AppBundle\Entity\UserMap;

class UserMapVoter extends Voter
{
    const EDIT = 'edit';

    /**
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(self::EDIT))) {
            return false;
        }

        if (!$subject instanceof UserMap) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        /** @var $userMap UserMap */
        $userMap = $subject; // $subject must be a UserMap instance, thanks to the supports method

        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return true;
        }

        $creator = $userMap->getAuthor();
        if (!$creator) return false;

        if ($user->getId() === $creator->getId()) {
            return true;
        }

        return false;
    }
}