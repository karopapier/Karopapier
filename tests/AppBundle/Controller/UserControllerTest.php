<?php

namespace Tests\AppBundle\Controller;

use Symfony\Component\BrowserKit\Client;


class UserControllerTest extends KaroWebTestCase
{
    /** @var Client $client */
    private $client = null;
    private $fixtures;

    public function testProtected()
    {
        // Create a new client to browse the application
        $client = $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/users/');
        $this->assertEquals(302, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /user/");
        $this->assertRegExp('/\/anmelden.php$/', $client->getResponse()->headers->get('location'));
        $this->assertNotRegExp('/Tagen/', $client->getResponse()->getContent());
    }

    public function testList()
    {
        $this->fixtures = $this->loadFixtures(array(
                'AppBundle\DataFixtures\ORM\LoadUserData',
        ))->getReferenceRepository();

        //find id for Didi
        $id = $this->fixtures->getReference("didi")->getId();

        // Create a new client to browse the application
        $client = static::createClient();
        $this->logIn($client, $id, "qwerasdf");

        // open /users
        $crawler = $client->request('GET', '/users/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /user/");
        $this->assertContains('Tagen', $client->getResponse()->getContent(), 'Cannot find "Tagen" in Response');

    }

    public function testEmailForAdmin()
    {
        $this->fixtures = $this->loadFixtures(array(
                'AppBundle\DataFixtures\ORM\LoadUserData',
        ))->getReferenceRepository();

        //find id for Didi
        $id = $this->fixtures->getReference("didi")->getId();

        // Create a new client to browse the application
        $client = static::createClient();
        $this->logIn($client, $id, "qwerasdf");

        //email for admin
        $crawler = $client->request('GET', '/users/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /user/");
        $this->assertRegExp('/didi@karoworld.de/', $client->getResponse()->getContent());

        //no email for user
        $client = static::createClient();
        $crawler = $client->request('GET', '/users/');
        $this->assertNotRegExp('/didi@karoworld.de/', $client->getResponse()->getContent());

    }
}
