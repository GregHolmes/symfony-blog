<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public function testCreateauthor()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/author/create');
    }

}
