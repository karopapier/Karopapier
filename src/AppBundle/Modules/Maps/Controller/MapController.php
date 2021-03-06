<?php

namespace AppBundle\Modules\Maps\Controller;

use AppBundle\Entity\Map;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MapController
{
    /**
     * @Route("/map/{id}", name="map_details", requirements={"id": "\d+"})
     * @Template("Maps/templates/mapdetails.html.twig")
     * @param Map $map
     */
    public function showAction(Request $request, Map $map)
    {
        return $map->toArray();
    }
}
