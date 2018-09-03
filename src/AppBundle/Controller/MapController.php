<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Map;
use AppBundle\Model\MapImage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapController
{
    /**
     * @Route("/map/{id}.{imgType}", name="map_img", requirements={"id": "\d+"})
     * @param Map $map
     */
    public function showImageAction(Request $request, Map $map)
    {
        //create Image
        $mi = new MapImage($map);
        // $mi->setBorder($border);
        // $mi->setFiletype($ftype);
        // if ($cps == 0) {
        // $mi->setSkipCheckpoints(true);
        // }

        //size from size or width/height?
        // if (isset($size)) {
        // $mi->setSize($size);
        // }

        // if (isset($height) || isset($width)) {
        // $mi->setSizeFromWidthAndHeight($width, $height);
        // }

        ob_start();
        $mi->render();
        $imageString = ob_get_clean();

        $headers = array(
            'Content-type' => 'image/png',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'no-cache',
        );


        return new Response($imageString, 200, $headers);
    }

    /**
     * @Route("/map/{id}", name="map_details", requirements={"id": "\d+"})
     * @Template("map/mapdetails.html.twig")
     * @param Map $map
     */
    public function showAction(Request $request, Map $map)
    {
        return $map->toArray();
    }

    /**
     * @Route("/map/", name="map_list")
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

}
