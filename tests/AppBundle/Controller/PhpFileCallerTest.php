<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Finder\Finder;

class PhpFileCallerTest extends WebTestCase
{
    private $client;

    public function __construct()
    {
        parent::__construct();


    }

    private function call($fname)
    {
        $url = 'http://www.karoworld.de/'.$fname;
        $ch = \curl_init($url);
        \curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
        \curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        \curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        $output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertLessThan(
            400,
            $httpcode,
            "Unexpected HTTP status code for GET /".$fname
        );

    }

    public function testCallEachFile()
    {
        return;
        $finder = new Finder();
        $files = $finder->files()->name('*.php')->depth(1)->in(__DIR__.'/../../../../')->sortByName();
        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            $fname = $file->getFilename();
            $this->call($fname);
        }
    }
}
