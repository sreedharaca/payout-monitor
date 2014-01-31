<?php

namespace Katana\SyncBundle\Tests\Lib;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Katana\SyncBundle\Lib\Loader\Url;


class UrlTest extends WebTestCase
{
    public function testSetGetUrl()
    {
        $url = 'http://ya.ru';

        $Url = new Url($url);

        $this->assertTrue($url == $Url->getUrl(), 'Inserted and got url are not equal.' . $Url->getUrl());
    }

    public function testWithPath()
    {
        $url = 'http://ya.ru/hui/';

        $Url = new Url($url);
        $resUrl = $Url->getUrl();

        $this->assertTrue($url == $resUrl, "Urls are not equal. Inserted: $url, got: $resUrl");
    }

    public function testWithParameter()
    {
        $url = 'http://ya.ru/hui/?limit=1';

        $Url = new Url($url);
        $resUrl = $Url->getUrl();

        $this->assertTrue($url == $resUrl, "Urls are not equal. Inserted: $url, got: $resUrl");
    }

    public function testAddParameter()
    {
        $url = 'http://ya.ru/hui/';

        $Url = new Url($url);
        $Url->setParameter('limit', 1);

        $resUrl = $Url->getUrl();

        $this->assertTrue($url.'?limit=1' == $resUrl, "Urls are not equal. Inserted: $url, got: $resUrl");
    }


    public function testChangeParameter()
    {
        $startUrl = 'http://ya.ru/hui/?limit=0';
        $checkUrl = 'http://ya.ru/hui/?limit=1';

        $Url = new Url($startUrl);
        $Url->setParameter('limit', 1);

        $resUrl = $Url->getUrl();

        $this->assertTrue($checkUrl == $resUrl, "Urls are not equal. Inserted: $checkUrl, got: $resUrl");
    }

    public function testAddSecondParameter()
    {
        $startUrl = 'http://ya.ru/hui/?limit=10';
        $checkUrl = 'http://ya.ru/hui/?limit=10&page=2';

        $Url = new Url($startUrl);
        $Url->setParameter('page', 2);

        $resUrl = $Url->getUrl();

        $this->assertTrue($checkUrl == $resUrl, "Urls are not equal. Inserted: $checkUrl, got: $resUrl");
    }
}
