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

    /**
     * Передаем в конструктор объект-массив офферов
     * Вычисляем лучший оффер
     */
    public function __construct(OfferGroup $offerGroup){

        $offers = $offerGroup->getOffers();

        if(!count($offers)){

            throw new \Exception('Нельзя передавать пустую группу OfferGroup в App');
        }

        $bestOffer = $offerGroup->findBestOffer();

        if( !$bestOffer ){
            throw new \Exception('Не удалось определить лучший оффер');
        }

        //разложить
        $this->main_offer = $bestOffer;

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