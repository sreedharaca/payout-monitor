<?php
/**
 * Created by PhpStorm.
 * User: Aslan
 * Date: 17.01.14
 * Time: 10:07
 */

namespace Katana\OfferBundle\Lib;


use Katana\OfferBundle\Entity\Offer;


class OfferGroup {

    private $offers = array();

    public function addOffer(Offer $offer)
    {
        $this->offers[] = $offer;
    }

    public function getOffers()
    {
        return $this->offers;
    }

    public function removeOffer(Offer $offerToRemove)
    {
        foreach($this->offers as $index => $loopOffer){

            if($offerToRemove->getId() == $loopOffer->getId()){

                unset($this->offers[$index]);

                return true;
            }
        }

        return false;
    }

    public function findBestOffer()
    {
        if(!count($this->offers)){
            return null;
        }

        //assume first element - is best offer
        $best_i = 0;

        foreach($this->offers as $i => $offer){

            if( $offer->betterThan($this->offers[$best_i]) ){
                $best_i = $i;
            }
        }

        return $this->offers[$best_i];
    }

    public function toArray()
    {
        $data = array();

        foreach($this->offers as $offer)
        {
            $OfferData = new OfferData($offer);
            $data[] = $OfferData->toArray();
        }

        return $data;
    }
} 