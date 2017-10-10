<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 20.05.2016
 * Time: 00:10
 */

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class ChatController
 * @Security("has_role('ROLE_USER')")
 * @package AppBundle\Controller\Api
 */
class ChatController extends AbstractApiController
{
    /**
     * @Route("chat")
     * @Method("POST")
     * @param Request $request
     */

    public function addAction(Request $request)
    {
        $user = $this->getUser();
        $data = $this->getJson($request);

        dump($data);
        list($text) = $this->requireKeys($data, array("msg"));
        dump($user);
        dump($text);

        $cs = $this->get("chat_service");
        $chatmessage = $cs->add($user, $text);

        $res = new JsonResponse();
        $res->setData($chatmessage->toApi());

        return $res;
    }
}