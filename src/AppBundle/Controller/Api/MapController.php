<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 20.05.2016
 * Time: 00:10
 */

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Map;
use Doctrine\DBAL\Connection;
use PDO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractApiController
{
    /**
     * @Route("/map/", name="api_map_list")
     */
    public function listAction(Request $request, Connection $connection)
    {
        $qb = $connection->createQueryBuilder();
        $qb->select('M_ID as id,name,author,cols,rows,rating,cps_list as cps, Starties as players');
        $qb->from('karo_maps');
        $qb->orderBy('id');

        $res = $qb->execute();
        $mapData = $res->fetchAll(PDO::FETCH_ASSOC);

        $response = new JsonResponse($mapData);
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
}
