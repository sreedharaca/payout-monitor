<?php

namespace Katana\SyncBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Katana\AffiliateBundle\Entity\Affiliate;
use Katana\SyncBundle\Lib\Curl\UrlResolver;
use Katana\SyncBundle\Lib\Curl\MetaRedirFinder;
use Katana\SyncBundle\Service\FinderManager\RedirectUrlFinderManager;

class CurlService
{
    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }


    public function catchRedirectUrl(Affiliate $Affiliate, $startUrl)
    {
        $resolver = new UrlResolver();

        $FinderManager = $this->container->get('RedirectUrlFinderManager');
        $Finder = $FinderManager->getFinder($Affiliate);

        $resolver->addFinder($Finder);

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

        $finalUrl = $resolver->getFinalUrl($startUrl);

        return $finalUrl;
    }

}