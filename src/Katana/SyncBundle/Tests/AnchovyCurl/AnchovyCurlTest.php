<?php

namespace Katana\SyncBundle\Tests\AnchovyCurl;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AnchovyCurlTest extends WebTestCase
{
    private $container;

    public function testEmtpy(){}

//    public function testGet()
//    {
//        $curl = $this->container->get('anchovy.curl')->setURL('http://ya.ru');
//
//        $content = $curl->execute();
//
//        $this->assertContains('artlebedev.ru', $content, 'Failed assertion: must contain artlevedev.ru');
//    }
//
//
//    public function testPostThenGet()
//    {
//        $url = 'http://clicksmob.com/analytics-new-api/login_api';
//        $post_params = ['api_email' => 'silverwindmg@gmail.com', 'api_password' => 'rus37katana'];
//
//        $curl = $this->container->get('anchovy.curl');
//
//        $curl->setURL($url);
//        $curl->setMethod('POST', $post_params);
//
//        $curl->setOptions([
//            'CURLOPT_COOKIEJAR' => "cookie.txt",
//            'CURLOPT_COOKIEFILE' => "cookie.txt"
//        ]);
//
//        $content = $curl->execute();
//
//        $this->assertContains('Ok.', $content, 'Failed authentication');
//
//
//        /** Get */
//        $offerurl = 'http://clicksmob.com/analytics-new-api/offers.json';
//        $curl->setURL($offerurl);
//        $curl->setMethod('GET', []);
//
//        $curl->setOptions([
//            'CURLOPT_COOKIEJAR' => "cookie.txt",
//            'CURLOPT_COOKIEFILE' => "cookie.txt"
//        ]);
//
//        $content = $curl->execute();
//
//        $this->assertJson($content, 'No json recieved.');
//    }



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
