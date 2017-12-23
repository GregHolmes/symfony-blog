<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogControllerTest extends WebTestCase
{
    public function testEntries()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/entries');
    }

    public function testEntry()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/entry/{slug}');
    }

    public function testAuthor()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/author/{name}');
    }

}
