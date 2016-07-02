<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testProtected()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/user/');
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /user/");
        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
    }
}
