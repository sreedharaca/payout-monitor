<?php

namespace Katana\SyncBundle\Service;

//use Katana\OfferBundle\KatanaOfferBundle;
//use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;


class Itunes
{
    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    //TODO не распознаются Уникум офферы
    //TODO показать список previewl_url для нераспознанных офферов
    //TODO обработка офферов с редиректами

    public function get($id)
    {
        if ( ! $id ) {
            return null;
        }

        $tuCurl = curl_init();
        curl_setopt($tuCurl, CURLOPT_URL, "https://itunes.apple.com/lookup?id=$id");
        //curl_setopt($tuCurl, CURLOPT_PORT , 443);
        curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
        curl_setopt($tuCurl, CURLOPT_HEADER, 0);
        curl_setopt($tuCurl, CURLOPT_SSLVERSION, 3);
        curl_setopt($tuCurl, CURLOPT_POST, 0);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $data);

        $tuData = curl_exec($tuCurl);

        if( curl_errno($tuCurl) ){
            return null;
        }

        curl_close($tuCurl);

        $result = json_decode( $tuData, true );

//        var_dump($result);exit;

        if( ! $result['resultCount'] ) {
            return null;
        }

        $data = array();

        $data['id'] 			= $id;
        $data['name'] 			= $result['results'][0]['trackName'];
//        $data['company'] 		= $result['results'][0]['artistName'];
        $data['iconUrl60'] 		= (isset($result['results'][0]['artworkUrl60'])?:'');
//        $data['rating'] 		= $result['results'][0]['averageUserRating'];
//        $data['ratingCount'] 	= $result['results'][0]['userRatingCount'];
//        $data['screenshotUrls'] = $result['results'][0]['screenshotUrls'];
        $data['description'] 	= (isset($result['results'][0]['description'])?:'');
//        $data['external_link']	= $result['results'][0]['trackViewUrl'];

        return $data;
    }

    /***
     * @param $url
     * @return integer Itunes App Id
     */
    public function parseUrl($url)
    {
        if(!strlen($url)){
            return false;
        }

        //если ссылка не айтюнсовая,
        if(strpos($url, 'itunes.apple.com') === false){
            //то делаем запрос на отлов редиректа и конечной ссылки
            try {
                $url = $this->container->get('CurlService')->catchRedirectUrl($url);
            }
            catch(\Exception $e){
                echo "Error while this->catchRedirectUrl($url): {$e->getMessage()}\n";
                return false;
            }

            if($url === false){
                return false;
            }
        }

        preg_match('/.*id[=]?(([\d]){9}).*/', $url, $match); //.*itunes

//        var_dump($match);

        if(!isset($match[1]) || !strlen($match[1]) ){
            return false;
        }

        return $match[1];
    }

//    public function catchRedirectUrl($url)
//    {
//        $options = array(
//            CURLOPT_RETURNTRANSFER => true,     // return web page
//            CURLOPT_HEADER         => true,    // return headers
//            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
//            CURLOPT_ENCODING       => "",       // handle all encodings
//            CURLOPT_USERAGENT      => "spider", // who am i
//            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
//            CURLOPT_CONNECTTIMEOUT => 20,      // timeout on connect
//            CURLOPT_TIMEOUT        => 20,      // timeout on response
//            CURLOPT_MAXREDIRS      => 30,       // stop after 10 redirects
//            CURLOPT_NOBODY         => true,       // stop after 10 redirects
//            CURLOPT_COOKIEJAR      => 'cookies.txt',
//            CURLOPT_COOKIEFILE     => 'cookies.txt',
//            CURLOPT_SSLVERSION     => 3,
//            CURLOPT_SSL_VERIFYPEER => false
//    );
//
//        $ch      = curl_init( $url );
//        curl_setopt_array( $ch, $options );
//        $content = curl_exec( $ch );
////        $err     = curl_errno( $ch );
////        $errmsg  = curl_error( $ch );
//        $header  = curl_getinfo( $ch, CURLINFO_EFFECTIVE_URL);
//        curl_close( $ch );
//
////        var_dump($header);
//
//        //$header['errno']   = $err;
//        // $header['errmsg']  = $errmsg;
//        //$header['content'] = $content;
////            print($header[0]);
//        if(!isset($header)){
//            return false;
//        }
//
//        return $header;
//    }
}