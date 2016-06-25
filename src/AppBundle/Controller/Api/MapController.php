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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractApiController
{
    /**
     * @Route("/map/{id}", name="api_map_show", requirements={"id": "\d+"})
     * @param Map $map
     */
    public function showAction(Map $map)
    {
        return new JsonResponse($map->toArray());
    }

    /**
     * @Route("/map/u{id}", name="api_usermap_show", requirements={"id": "\d+"})
     * @param UserMap $map
     */
    public function showCustomAction(UserMap $map)
    {
        return new JsonResponse($map->toArray());
    }
}
