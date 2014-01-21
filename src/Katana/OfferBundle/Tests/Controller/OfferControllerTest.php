<?php

namespace Katana\OfferBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OfferControllerTest extends WebTestCase
{
        private $em;
//
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    /***
     *
     */
    public function testFindCompetitors()
    {
//        $id = 1900;

//        $offer = $this->em->getRepository('KatanaOfferBundle:Offer')->find($id);

//        if(emtpy($offer)){
//            $this->fail("Оффер # $id - не найден.");
//        }

//        $offers = $this->em->getRepository('KatanaOfferBundle:Offer')->findCompetitors($id);

//        $this->assertTrue(count($offers)>0);

//        echo "Competitors count: " . count($offers) . "\n";
    }
}
