<?php

namespace Tests\AppBundle\Controller;

use Symfony\Component\BrowserKit\Client;
use Tests\AppBundle\Controller\KaroWebTestCase;


class UserControllerTest extends KaroWebTestCase
{
    /** @var Client $client */
    private $client = null;

    public function testProtected()
    {
        // Create a new client to browse the application
        $client = $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/user/');
        $this->assertEquals(302, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /user/");
        $this->assertRegExp('/\/anmelden.php$/', $client->getResponse()->headers->get('location'));
        $this->assertNotRegExp('/Tagen/', $client->getResponse()->getContent());
    }

    public function testList()
    {
        // Create a new client to browse the application
        $client = static::createClient();
        $this->logIn($client, 1, "asdf");

        // Create a new entry in the database
        $crawler = $client->request('GET', '/user/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /user/");
        $this->assertNotRegExp('/Tagen/', $client->getResponse()->getContent());

    }
}
