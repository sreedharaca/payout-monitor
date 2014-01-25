<?php

namespace Katana\OfferBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Katana\OfferBundle\Entity\Offer;
use Katana\DictionaryBundle\Entity\Platform;


class AppService {

    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function parseIdByOffer(Offer $Offer){

        $Platform = $Offer->getPlatform();

        if(empty($Platform)){
            return false;
        }

        if($Platform == Platform::IOS){

            $ItunesService = $this->container->get('Itunes');

            return $id = $ItunesService->parseUrl($Offer->getFinalUrl());

        }
        elseif($Platform == Platform::ANDROID){

            $PlayGoogleService = $this->container->get('PlayGoogle');

            return $id = $PlayGoogleService->parseUrl($Offer->getFinalUrl());

        }
        elseif($Platform == Platform::WEB){

            $parts = parse_url($Offer->getFinalUrl());

            if(  isset($parts['host'])  &&  strlen($parts['host'])  ){

                return $id = $parts['host'];
            }
        }
        else{
            $id = false;
        }

        return $id;
    }

}