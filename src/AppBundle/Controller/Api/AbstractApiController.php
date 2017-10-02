<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 20.05.2016
 * Time: 00:11
 */

namespace AppBundle\Controller\Api;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractApiController extends Controller
{
    public function getJson(Request $request)
    {
        $content = $request->getContent();
        //dump($content);
        if (!($content)) return "";
        $json = json_decode($content, true);
        return $json;
    }

    public function sendError($code = 404, $msg = "NOT_FOUND")
    {
        $response = new JsonResponse();
        $response->setStatusCode($code);
        $response->setData($msg);
        return $response;
    }

    function requireKeys($data, $keys)
    {
        $returns = array();
        foreach ($keys as $key) {
            if (!(isset($data[$key]))) {
                throw new \HttpInvalidParamException("MISS_KEY");
            }
            $returns[] = $data[$key];
        }
        return $returns;
    }
}