<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 26.10.2017
 * Time: 12:34
 */

namespace AppBundle\Listener;


use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class DevListener
{
    private $env;
    private $livereload;

    public function __construct($env, $livereload)
    {
        $this->env = $env;
        $this->livereload = $livereload;
    }

    private function isDev()
    {
        return "dev" === $this->env;
    }

    /**
     * Include livereload script
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$this->isDev()) {
            return;
        }
        $write = "<script>document.write('<scr' + 'ipt src=\"".$this->livereload."\"></scr' + 'ipt>')</script>";
        $response = $event->getResponse();
        $content = $response->getContent();
        $content = str_replace("</body>", $write."</body>", $content);
        $response->setContent($content);
    }
}