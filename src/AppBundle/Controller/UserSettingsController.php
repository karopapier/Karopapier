<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Module\UserSettings\Form\UserSettingsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserSettingsController
 * @package AppBundle\Controller
 * @Security("has_role('ROLE_USER')")
 */
class UserSettingsController extends Controller
{
    /**
     * @Route("/einstellungen", name="user_settings")
     * @param Request $request
     * @Template("user/settings.html.twig")
     *
     * @return array
     */
    public function settings(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $userSettingsData = $user->getUserSettingsData();
        // create a task and give it some dummy data for this example
        $form = $this->createForm(UserSettingsType::class, $userSettingsData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->updateSettings($userSettingsData);
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', 'Ging');
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
