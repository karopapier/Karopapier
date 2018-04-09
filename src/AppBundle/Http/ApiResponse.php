<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 09.04.2018
 * Time: 09:37
 */

namespace AppBundle\Http;


use AppBundle\Interfaces\ApiControllerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class ApiResponse implements EventSubscriberInterface
{

    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    /**
     * Mark request in controllers that implement ApiControllerInterface
     *
     * @param FilterControllerEvent $event
     */
    public function checkForApi(FilterControllerEvent $event)
    {
        list($controllerClass, $action) = $event->getController();
        if (!($controllerClass instanceof ApiControllerInterface)) {
            dump('Ne, kein Interface');

            return;
        }

        $request = $event->getRequest();
        $request->attributes->set('api', true);
    }

    /**
     * Add meta information to requests marked as being 'api'
     *
     * @param FilterResponseEvent $event
     * @return \Symfony\Component\HttpFoundation\Response|void
     */
    public function addMeta(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->attributes->get('api')) {
            return;
        }

        $response = $event->getResponse();

        if ($this->user) {
            $response->headers->set('login', $this->user->getUsername());
        }
        $response->headers->set('v', 1);
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => [
                'checkForApi',
            ],
            KernelEvents::RESPONSE => [
                'addMeta',
            ],
        ];
    }
}