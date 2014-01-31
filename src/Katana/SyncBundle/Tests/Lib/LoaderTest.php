<?php

namespace Katana\SyncBundle\Tests\Lib;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Katana\SyncBundle\Lib\Loader\RawDataCollection;
new \Doctrine\Common\Collections\ArrayCollection;
use Katana\AffiliateBundle\Entity\Affiliate;

class LoaderTest extends WebTestCase
{
    private $container;


    public function testLoaderHasNotErrors()
    {
        $Loader = $this->container->get('AffiliateDataLoader');

        $this->assertTrue(false === $Loader->hasErrors(), 'checking $Loader->hasErrors() works.');
    }


    public function testLoaderGetErrorsMethod()
    {
        $Loader = $this->container->get('AffiliateDataLoader');

        $this->assertTrue(array() === $Loader->getErrors(), 'loader->getErrors didnt return array.');
    }


//    public function testLoadFullyMethod()
//    {
//        $Loader = $this->container->get('AffiliateDataLoader');
//
//        $url = 'http://cpa.katanaads.com/offers/offers.json?api_key=AFFtX40vixsn9GbnqCZtM6MWxwPhnZ&limit=1';
//
//        $Affiliate = new Affiliate();
//        $Affiliate->setApiUrl($url);
//
//        $Collection = $Loader->load($Affiliate);
//
//        $this->assertTrue($Collection instanceof ArrayCollection, 'It must be instance of ArrayCollection.');
//        $this->assertTrue(count($Collection) == 1, 'ArrayCollection must contain right one object.');
//    }

//    public function testGetTotalRows()
//    {
//        $Loader = $this->container->get('AffiliateDataLoader');
//
//        $url = 'http://cpa.katanaads.com/offers/offers.json?api_key=AFFtX40vixsn9GbnqCZtM6MWxwPhnZ&limit=1';
//
//        $Affiliate = new Affiliate();
//        $Affiliate->setApiUrl($url);
//
//        $totalRows = $Loader->getTotalRows($Affiliate);
//
//        $this->assertTrue(is_int($totalRows), 'Must be an integer.');
//        $this->assertTrue($totalRows > 1, 'Must be positive number.');
//    }

//    public function testLoadPartiallyMethod()
//    {
//        $Loader = $this->container->get('AffiliateDataLoader');
//
//        $url = 'http://affiliates.kissmyads.com/offers/offers.json?api_key=AFFb88Vt0UdG6gA3at7xicYdkzl78H&limit=1';
//
//        $Affiliate = new Affiliate();
//        $Affiliate->setApiUrl($url);
//        $Affiliate->setPartialLoading(true);
//
//        $Collection = $Loader->load($Affiliate);
//
//        $this->assertTrue($Collection instanceof ArrayCollection, 'It must be instance of ArrayCollection.');
//        $this->assertTrue(count($Collection) > 1, 'ArrayCollection must contain more than one object.');
//    }


    /**
    {"success":true,"rows":1,"total_rows":17,"data":{"offers":[{"id":"1296","name":"Banda [Android] RU","description":null,"payout_type":"cpa_flat","protocol":"server","expiration_date":"2015-01-29","preview_url":"https:\/\/play.google.com\/store\/apps\/details?id=com.alawar.banda&hl=ru","currency":"USD","tracking_url":"http:\/\/play.googles.ru.com\/aff_c?offer_id=1296&aff_id=1154","payout":"1.00","categories":"Android","countries":"Russian Federation","countries_short":"RU"}]}}
     */

    /***
     * Set Up container
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->container = static::$kernel->getContainer();
    }
}
