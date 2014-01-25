<?php

namespace Katana\OfferBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Katana\OfferBundle\Entity\Offer;
use Katana\DictionaryBundle\Entity\Platform;


class PlatformService {

    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function guessByOffer(Offer $Offer)
    {
        /** try by preview url */
        $Platform = $this->guessByUrl($Offer->getFinalUrl());

        if(!empty($Platform)) {
            return $Platform;
        }

        /*** try by device */
        foreach($Offer->getDevices() as $device){

            $Platform = $device->getPlatform();

            if( !empty($Platform) && ($Platform instanceof Platform) ){
                return $Platform;
            }
        }

        return false;
    }

    public function guessByUrl($url){

        $em = $this->container->get('doctrine')->getManager();

        if( strpos($url, 'itunes.apple.com') !== false ){
            return $Platform = $em->getRepository('KatanaDictionaryBundle:Platform')->findOneBy(array('name'=>Platform::IOS));
        }
        elseif( strpos($url, 'play.google.com') !== false ){
            return $Platform = $em->getRepository('KatanaDictionaryBundle:Platform')->findOneBy(array('name'=>Platform::ANDROID));
        }
        else{
            return $Platform = $em->getRepository('KatanaDictionaryBundle:Platform')->findOneBy(array('name'=>Platform::WEB));;
        }
    }

    public function guessByRawString($str){

        $rules = array(
            Platform::IOS       => array('iphone', 'ipad', 'ios'),
            Platform::ANDROID   => array('android'),
            Platform::WEB       => array('web', 'display')
        );

        foreach($rules as $platform => $words){

            foreach($words as $word){

                if( strpos( strtolower($str), strtolower($word) ) !== false ){

                    $em = $this->container->get('doctrine')->getManager();

                    return $em->getRepository('KatanaDictionaryBundle:Platform')->findOneBy(array('name'=>$platform));
                }
            }
        }

        return false;
    }

}