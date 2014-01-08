<?php

namespace Katana\SyncBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;


class CurlService
{
    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }


    public function catchRedirectUrl($url)
    {
        $options = array(
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => true,    // return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_USERAGENT      => "spider", // who am i
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 20,      // timeout on connect
            CURLOPT_TIMEOUT        => 20,      // timeout on response
            CURLOPT_MAXREDIRS      => 30,       // stop after 10 redirects
            CURLOPT_NOBODY         => true,       // stop after 10 redirects
            CURLOPT_COOKIEJAR      => 'cookies.txt',
            CURLOPT_COOKIEFILE     => 'cookies.txt',
            CURLOPT_SSLVERSION     => 3,
            CURLOPT_SSL_VERIFYPEER => false
        );

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );

        $content = curl_exec( $ch );

//        $err     = curl_errno( $ch );
//        $errmsg  = curl_error( $ch );

        $header  = curl_getinfo( $ch, CURLINFO_EFFECTIVE_URL);

        curl_close( $ch );

//        var_dump($header);

        //$header['errno']   = $err;
        // $header['errmsg']  = $errmsg;
        //$header['content'] = $content;
//            print($header[0]);

        if(!isset($header)){
            return false;
        }

        return $header;
    }
}