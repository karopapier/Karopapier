<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Map;
use AppBundle\Map\MapImageRenderer;
use AppBundle\Model\MapImage;
use AppBundle\ValueObject\MapImageOptions;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapController
{
    /**
     * @Route("/map/{id}.{filetype}", name="map_img", requirements={"id": "\d+"})
     * @param Request $request
     * @param Map $map
     * @param $filetype
     * @param MapImageRenderer $mapImageRender
     * @return Response
     * @throws \Exception
     */
    public function showImageAction(Request $request, Map $map, $filetype, MapImageRenderer $mapImageRender)
    {
        $options = new MapImageOptions();
        $options->setFileType($filetype);
        $options->setSize($request->get('size', 12));
        $options->setBorder($request->get('border', 12));

        $mapImage = new MapImage($map, $options);

        if ($mapImage->isCached()) {
            return new RedirectResponse($mapImage->getUrl());
        }

        $binary = $mapImageRender->getImageString($mapImage);

        //create Image
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

        $headers = array(
            'Content-type' => 'image/'.$filetype,
            'Pragma' => 'no-cache',
            'Cache-Control' => 'no-cache',
            'Lala' => (new \DateTime())->format('H:m:s'),
        );

        return new Response($binary, 200, $headers);
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
