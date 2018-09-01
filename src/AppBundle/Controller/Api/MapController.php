<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 20.05.2016
 * Time: 00:10
 */

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Map;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractApiController
{
    /**
     * @Route("/map/", name="api_map_list")
     */
    public function listAction(Request $request)
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Map');
        /** @var Map[] $maps */
        $maps = $repo->getActiveMaps();
        $data = [];
        foreach ($maps as $map) {
            $data[] = $map->toArray();
        }

        $response = new JsonResponse($data);
        $response->setCallback($request->get("callback"));

        return $response;
    }

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
     * @Route("/mapcode/{id}", name="api_mapcode_show", requirements={"id": "\d+"})
     * @param Map $map
     */
    public function mapcodeAction(Request $request, Map $map)
    {
        $response = new JsonResponse($map->getCode());
        $response->setCallback($request->get("callback"));

        return $response;
    }
}
