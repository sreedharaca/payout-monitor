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
        $Platform = $this->guessByPreviewUrl($Offer->getPreviewUrl());

        if(!empty($Platform)) {
            return $Platform;
        }

        /*** try by device */
        foreach($Offer->getDevices() as $device){

            $Platform = $device->getPlatform();

            if( !empty($Platform) && is_object($Platform) ){
                return $Platform;
            }
        }

        return false;
    }

    public function guessByPreviewUrl($url){

        if( strpos($url, 'itunes.apple.com') !== false ){

            $em = $this->container->get('doctrine')->getManager();
            $Platform = $em->getRepository('KatanaDictionaryBundle:Platform')->findOneBy(array('name'=>Platform::IOS));

            return $Platform;
        }
        elseif( strpos($url, 'play.google.com') !== false ){

            $em = $this->container->get('doctrine')->getManager();
            $Platform = $em->getRepository('KatanaDictionaryBundle:Platform')->findOneBy(array('name'=>Platform::ANDROID));

            return $Platform;
        }
        else{
            return false;
        }
    }

    public function guessByRawString($str){

        $rules = array(
            Platform::IOS       => array('iphone', 'ipad', 'ios'),
            Platform::ANDROID   => array('android'),
            Platform::WEB       => array('web')
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