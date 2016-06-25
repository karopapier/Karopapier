<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 20.05.2016
 * Time: 00:10
 */

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Map;
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
}
