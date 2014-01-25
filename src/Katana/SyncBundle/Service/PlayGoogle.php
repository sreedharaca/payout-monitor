<?php

namespace Katana\SyncBundle\Service;

//use Katana\OfferBundle\KatanaOfferBundle;
//use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;


class PlayGoogle
{
    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    //TODO не распознаются Уникум офферы
    //TODO показать список previewl_url для нераспознанных офферов
    //TODO обработка офферов с редиректами

    /***
     * Получить финальную ссылку (с редиректами)
     * и из полученного урла получить параметр id
     *
     * @param $url
     * @return integer PlayGoogle Application Id
     */
    public function parseUrl($url)
    {
        if(!strlen($url)){
            return false;
        }

        //если ссылка не google,
//        if(strpos($url, 'play.google.com') === false){
//
//            //Получить финальную ссылку
//            try {
//                $url = $this->container->get('CurlService')->catchRedirectUrl($url);
//            }
//            catch(\Exception $e){
//                echo "Error while this->catchRedirectUrl($url): {$e->getMessage()}\n";
//                return false;
//            }
//
//            //Проверим результат получения финальную ссылку
//            if($url === false){
//                return false;
//            }
//        }

        $query_string = parse_url($url, PHP_URL_QUERY );
        parse_str($query_string, $params);

        if(!isset($params['id'])){
            return false;
        }

        return $params['id'];
    }

}