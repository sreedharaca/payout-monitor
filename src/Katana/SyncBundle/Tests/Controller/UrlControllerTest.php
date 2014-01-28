<?php

namespace Katana\SyncBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Katana\SyncBundle\Lib\Curl\MetaRedirFinder;
use Katana\SyncBundle\Lib\Curl\JsRedirFinder;
use Katana\SyncBundle\Lib\Curl\Curl;
use Katana\SyncBundle\Lib\Curl\UrlResolver;

class UrlControllerTest extends WebTestCase
{

    public function testMetaRedirFinder()
    {
        $url = 'http://hastrk1.com/serve?action=click&publisher_id=17904&site_id=15214&offer_id=248464&ref_id=&device_id=&mac_address=&odin=&ios_ifa=&sub_publisher=192920&sub_site=78554&sub_campaign=739&sub_adgroup=2293&sub_ad=';

        /** 1 */
        $html = '<html><head><META http-equiv="refresh" content="0; URL=\'http://hastrk1.com/serve?action=click&publisher_id=17904&site_id=15214&offer_id=248464&ref_id=&device_id=&mac_address=&odin=&ios_ifa=&sub_publisher=192920&sub_site=78554&sub_campaign=739&sub_adgroup=2293&sub_ad=\'"></head></html>';

        $finder = new MetaRedirFinder();
        $finder->setHtml($html);

        $this->assertTrue($url == $finder->findUrl($html), '**Error parsing url from Meta**');

        /** 2 */
        $finder = new MetaRedirFinder();
        $finder->setHtml('');

        $this->assertTrue(false === $finder->findUrl($html), '**Error parsing url from Meta**');
    }

    public function testJsRedirFinder()
    {
        $url = 'http://ya.ru/?rnd=123';

        /** 1 */
        $html = "<html><head><script>window.location=\"http://ya.ru/?rnd=123\";</script></head></html>";

        $finder = new JsRedirFinder();
        $finder->setHtml($html);

        $this->assertTrue($url == $finder->findUrl($html), '**Error parsing url from JavaScript**');


        /** 2 */
        $html = "<html><head><script> window.location.replace( 'http://ya.ru/?rnd=123') ;</script></head></html>";

        $finder = new JsRedirFinder($html);
        $finder->setHtml($html);

        $this->assertTrue($url == $finder->findUrl($html), '**Error parsing url from JavaScript**');
    }


    public function testGetEffectiveUrl()
    {
        $url = 'http://ya.ru/';

        $options = array(
            CURLOPT_URL             => 'http://ya.ru',
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1", // who am i
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
//        CURLOPT_HTTPHEADER      => array("Content-Type:application/x-www-form-urlencoded"),
            CURLOPT_NOBODY         => false,       // stop after 10 redirects
            //            CURLOPT_COOKIEJAR      => 'cookies.txt',
            //            CURLOPT_COOKIEFILE     => 'cookies.txt',
            CURLOPT_SSLVERSION     => 3,
            CURLOPT_SSL_VERIFYPEER => false
        );

        $curl = new Curl($options);

        $this->assertTrue($url == $curl->getEffectiveUrl(), '**Getting Effective Url');
    }

    public function testResolveFinalUrl()
    {
        $startUrl = 'http://c.mobpartner.mobi/?s=559571';
        $finalUrl = "itunes.apple.com/";

        $resolver = new UrlResolver();

        $resolver->addFinder(new MetaRedirFinder());

        $options = array(
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1", // who am i
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            CURLOPT_NOBODY         => false,       // stop after 10 redirects
            //CURLOPT_HTTPHEADER      => array("Content-Type:application/x-www-form-urlencoded"),
            //CURLOPT_COOKIEJAR      => 'cookies.txt',
            //CURLOPT_COOKIEFILE     => 'cookies.txt',
            CURLOPT_SSLVERSION     => 3,
            CURLOPT_SSL_VERIFYPEER => false
        );

        $resolver->setCurlOptions($options);

        $fUrl = $resolver->getFinalUrl($startUrl);


        $this->assertTrue(strpos($fUrl, $finalUrl) !== false, 'Resolving final url' );
    }
}
