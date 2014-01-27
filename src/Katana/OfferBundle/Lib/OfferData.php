<?php
/**
 * Created by PhpStorm.
 * User: Aslan
 * Date: 17.01.14
 * Time: 11:57
 */

namespace Katana\OfferBundle\Lib;


use Katana\OfferBundle\Entity\Offer;


class OfferData {

    private $offer;

    public function __construct(Offer $offer){

        $this->offer = $offer;
    }

    public function toArray(){

        /*** Icon */
        $icon = '';

        $App = $this->offer->getApp();

        if(!empty($App)){
            $icon = $App->getIconUrl();
        }

        /*** Device */
        $device_name = '';
        $devices = $this->offer->getDevices();
        if(count($devices)){
            $device = $devices[0];
            $device_name = $device->getName();
        }

        /*** Countries */
        $countries = array();
        foreach($this->offer->getCountries() as $country){
            $countries[] = $country->getCode();
        }


        $offerItem = array(
            'id'            => $this->offer->getId(),
            'icon'          => $icon,
            'partner'       => $this->offer->getAffiliate()->getName(),
            'name'          => $this->offer->getName(),
            'payout'        => $this->offer->getPayout(),
            'device'        => $device_name,
            'countries'     => $countries,
            'externalUrl' => $this->offer->getAffiliate()->generateOfferUrl($this->offer->getExternalId()),
            'is_incentive'  => $this->offer->getIncentive(),
            'is_new'        => $this->offer->getNew(),
            'platform'      => $this->offer->getPlatform()->getName(),
            'platformIconUrl'=> $this->offer->getPlatform()->getIconUrl()
        );

        return $offerItem;
    }
} 