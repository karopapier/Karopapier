<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 20.05.2016
 * Time: 00:10
 */

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Map;
use AppBundle\Entity\UserMap;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractApiController
{
    /**
     * @Route("/map/{id}", name="api_map_show", requirements={"id": "\d+"})
     * @param Map $map
     */
    public function showAction(Request $request, Map $map)
    {
        $response = new JsonResponse($map->toArray());
        $response->setCallback($request->get("callback"));

        return $response;
    }

    /**
     * @Route("/map/u{id}", name="api_usermap_show", requirements={"id": "\d+"})
     * @param UserMap $map
     */
    public function showCustomAction(Request $request, UserMap $map)
    {
        $response = new JsonResponse($map->toArray());
        $response->setCallback($request->get("callback"));

        return $response;
    }
}
