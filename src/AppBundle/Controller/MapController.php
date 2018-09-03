<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Map;
use AppBundle\Map\MapImageCache;
use AppBundle\Map\MapImageRenderer;
use AppBundle\Model\MapImage;
use AppBundle\ValueObject\MapImageOptions;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MapController
{
    /**
     * @Route("/map/{id}.{filetype}", name="map_img", requirements={"id": "\d+"})
     * @param Request $request
     * @param Map $map
     * @param $filetype
     * @param MapImageRenderer $mapImageRender
     * @return RedirectResponse
     * @throws \Exception
     */
    public function showImageAction(Request $request, Map $map, $filetype, MapImageCache $mapCache)
    {
        /** @var MapImageOptions $options */
        $options = new MapImageOptions();
        $thumbnail = (bool)$request->get('thumb', false);

        // Thumbnail or size?
        if ($thumbnail) {
            $options->setSize(1);
            $options->setBorder(0);
        } else {
            $options->setSize($request->query->getInt('size', 12));
            $options->setBorder($request->query->getInt('border', 1));
        }

        $options->setFileType($filetype);
        $options->setCps($request->query->getInt('cps', 1));
        $mapImage = new MapImage($map, $options);

        // Height/Width
        $height = $request->query->getInt('height', 0);
        $width = $request->query->getInt('width', 0);
        if ($width > 0 || $height > 0) {
            $mapImage->setSizeByWidthOrHeight($width, $height);
        }

        $url = $mapCache->getUrl($mapImage);

        return new RedirectResponse($url);
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
