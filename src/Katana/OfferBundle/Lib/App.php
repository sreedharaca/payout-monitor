<?php
/**
 * Created by PhpStorm.
 * User: Aslan
 * Date: 17.01.14
 * Time: 10:09
 */

namespace Katana\OfferBundle\Lib;

use Katana\OfferBundle\Lib\OfferGroup;
use Katana\OfferBundle\Entity\Offer;


class App {

    private $main_offer;

    private $offerGroup;

    public function __construct(OfferGroup $offerGroup){

        $offers = $offerGroup->getOffers();

        if(!count($offers)){

            throw new \Exception('Нельзя передавать пустую группу OfferGroup в App');
//            $this->main_offer = null;
//            $this->offerGroup = new OfferGroup();
//
//            return;
        }

        $best_offer_index = null;
        foreach($offers as $index => $offer){

            //assume first element - is best offer
            if($best_offer_index === null){
                $best_offer_index = $index;
//                continue;
            }

            if($offers[$best_offer_index]->getPayout() < $offer->getPayout()){
                $best_offer_index = $index;
            }
        }

        //разложить
        $this->main_offer = $offers[$best_offer_index];

        $offerGroup->removeOffer($this->main_offer);

        $this->offerGroup = $offerGroup;
    }

    public function getLetter(){

        $name = $this->getMainOffer()->findName();

        if(!$name){
            return null;
        }

        return strtoupper( mb_substr($name, 0, 1, 'utf-8') );
    }

    /**
     * @param null $main_offer
     */
    public function setMainOffer(Offer $main_offer)
    {
        $this->main_offer = $main_offer;
    }

    /**
     * @return null
     */
    public function getMainOffer()
    {
        return $this->main_offer;
    }

    /**
     * @param \Katana\OfferBundle\Lib\OfferGroup $offerGroup
     */
    public function setOfferGroup(OfferGroup $offerGroup)
    {
        $this->offerGroup = $offerGroup;
    }

    /**
     * @return \Katana\OfferBundle\Lib\OfferGroup
     */
    public function getOfferGroup()
    {
        return $this->offerGroup;
    }



} 