<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 18.07.2016
 * Time: 23:57
 */

namespace AppBundle\Twig;


use AppBundle\Entity\User;
use AppBundle\Services\MessagingService;

class MessagingExtension extends \Twig_Extension
{
    /** @var MessagingService $ms */
    private $ms;

    public function __construct(MessagingService $messagingService)
    {
        $this->ms = $messagingService;
    }

    public function getUnreadCounter(User $user)
    {
        return $this->ms->getUnreadCounter($user);
    }

    public function getFunctions()
    {
        return array(
            'getUnreadCounter' => new \Twig_SimpleFunction("getUnreadCounter", array($this, "getUnreadCounter")),
        );
    }

    public function getName()
    {
        return "messaging_extension";
    }
}