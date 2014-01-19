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

    public function removeOffer(Offer $offer){

        $del_index = null;
        foreach($this->offers as $index => $loop_offer){

            if($offer->getId() == $loop_offer->getId()){

                $del_index = $index;
            }
        }

        if($del_index === null){

            return false;
        }

        unset($this->offers[$del_index]);

        return true;
    }

} 