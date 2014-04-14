<?php

namespace Katana\SyncBundle\Service;

use Katana\OfferBundle\Entity\PayoutHistory;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Katana\OfferBundle\Entity\Offer;


class OfferFactory
{
    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function createFromArray($row)
    {
        $Offer = new Offer();
        $Offer->setAffiliate($row['affiliate']);
        $Offer->setName($row['name']);
        $Offer->setExternalId($row['external_id']);
        //Payout
        $Offer->setPayout($row['payout']);
        //Payout History
        $Payout = new PayoutHistory( $row['payout'] );
        $Payout->setOffer($Offer);
        $Offer->addPayout( $Payout );

        $Offer->setPreviewUrl($row['preview_url']);
        $Offer->setActive(true);
        $Offer->setDeleted(false);
        $Offer->setJson(null);

        /***
         * Device
         */
        foreach($row['devices'] as $device)
        {
            $Offer->addDevice($device);
        }

        /***
         * Country
         */
        foreach($row['countries'] as $country)
        {
            $Offer->addCountrie($country);
        }

        return $Offer;
    }


    public function updateFromArray(Offer &$Offer, $row)
    {
        $changes = array();

        /** Name */
        if($Offer->getName() != $row['name'])
        {
            $changes['name'] = array('old' => $Offer->getName(), 'new' => $row['name']);

            $Offer->setName($row['name']);
        }

        /** Payout */
        if($Offer->getPayout() != $row['payout'])
        {
            $changes['payout'] = array('old' => $Offer->getPayout(), 'new' => $row['payout']);

            $Offer->setPayout($row['payout']);

            //добавить в Offer дочернюю сущность PayoutHistory
            $Payout = new PayoutHistory( $changes['payout']['new'] );
            $Payout->setOffer($Offer);
            $Offer->addPayout( $Payout );

//            $EventLog->save(Log::ACTION_PAYOUT_CHANGE, "Было {$Offer->getPayout()} стало {$row['payout']}", $Offer);
        }

        /** Preview Url */
        if($Offer->getPreviewUrl() != $row['preview_url'])
        {
            $changes['preview_url'] = array('old' => $Offer->getPreviewUrl(), 'new' => $row['preview_url']);

            $Offer->setPreviewUrl($row['preview_url']);
        }

        /** Active */
        if(!$Offer->getActive())
        {
            $changes['active'] = array('old' => false, 'new' => true);

            $Offer->setActive(true);
        }

        /** Deleted */
        if($Offer->getDeleted())
        {
            $changes['deleted'] = array('old' => true, 'new' => false);

            $Offer->setDeleted(false);
        }

        /** Devices */
        $old_ids = array();
        $new_ids = array();

        foreach($Offer->getDevices() as $device){
            $old_ids[] = $device->getId();
        }

        foreach($row['devices'] as $device){
            $new_ids[] = $device->getId();
        }

        $insert_ids = array_diff($new_ids, $old_ids);
        $remove_ids = array_diff($old_ids, $new_ids);

        //если есть различия
        if(count($insert_ids) || count($remove_ids)){

            $changes['device'] = array('old' => $old_ids, 'new' => $new_ids);

            $EntityManager = $this->container->get('doctrine')->getManager();

            foreach($insert_ids as $id){
                $device = $EntityManager->getRepository('KatanaDictionaryBundle:Device')->find($id);
                $Offer->addDevice($device);
            }

            foreach($Offer->getDevices() as $device){

                if(in_array($device->getId(), $remove_ids)){
                    $Offer->removeDevice($device);
                }
            }
        }


        /** Countries */
        $old_ids = array();
        $new_ids = array();

        foreach($Offer->getCountries() as $country){
            $old_ids[] = $country->getId();
        }

        foreach($row['countries'] as $country){
            $new_ids[] = $country->getId();
        }

        $insert_ids = array_diff($new_ids, $old_ids);
        $remove_ids = array_diff($old_ids, $new_ids);

        //если есть различия
        if(count($insert_ids) || count($remove_ids)){

            $changes['country'] = array('old' => $old_ids, 'new' => $new_ids);

            $EntityManager = $this->container->get('doctrine')->getManager();

            foreach($insert_ids as $id){
                $country = $EntityManager->getRepository('KatanaDictionaryBundle:Country')->find($id);
                $Offer->addCountrie($country);
            }

            foreach($Offer->getCountries() as $country){

                if(in_array($country->getId(), $remove_ids)){
                    $Offer->removeCountrie($country);
                }
            }
        }


        return $changes;
    }

}