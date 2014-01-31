<?php

namespace Katana\JsonParserBundle\Parser;

use Katana\JsonParserBundle\Parser\BaseParser;

class Ad4gameJsonParser extends BaseParser {

//    private $json = '"28":{"OfferId":"28","OfferName":"Conquer Online","Platform":"Display","DailyCap":"None","PreviewUrl":"http:\/\/co.91.com\/","PreviewImage":"http:\/\/cdn.ad4game.com\/1a7ed3a45cbff9f5d3fae4f0cc32091c.jpg","TrackingUrl":"http:\/\/ads.ad4game.com\/www\/delivery\/dck.php?offerid=28&zoneid=33039","Description":"Create your character, gather your allies, and unite against evildoers. Come and start your mythological journey now!","RestrictedTraffic":" Social traffic (facebook, etc), Social traffic (facebook, etc) Email MarketingM, Brand Bidding","Restrictions":"Single Opt-In (SOI)\r\nAllowed Traffic:\r\nAbsolutely no Incentivization\r\nNo Search\r\nNo Co-Reg\r\nNo Facebook Traffic","countries":[{"CountryName":"SA","OptIn":"SOI","endDate":"2013-12-27 16:50:47","Rate":"0.32"}]}';

    public function parse( $json = null )
    {
//        if(!$json){ $json = $this->json; }

        $array = json_decode($json, true);

//        var_dump($array['Offers']);

        if(!isset($array['Offers'])){
            return false;
        }

        $data = array();
        foreach($array['Offers'] as $id => $offer)
        {
            $row['external_id'] = (int)$offer['OfferId'];
            $row['name'] = strval( $offer['OfferName'] );
            $row['preview_url'] = $offer['PreviewUrl'];
            $row['json'] = json_encode($offer);

            $payouts = array();
            $row['countries'] = array();

            foreach($offer['countries'] as $country)
            {
                $payouts[] = floatval($country['Rate']);

                $country_code = trim($country['CountryName']);

                $Country = $this->container->get('doctrine')->getRepository('KatanaDictionaryBundle:Country')->findOneByCode($country_code);

                if(is_object($Country)){
                    $row['countries'][] = $Country;
                }

            }

            $row['payout'] = max($payouts);

            $row['devices'] = array();

            $Device = $this->container->get('doctrine')->getRepository('KatanaDictionaryBundle:Device')->findInString($offer['Platform']);

            if(is_object($Device)){
                $row['devices'][] = $Device;
            }


            /***
             * Платформа
             */
            $PlatformService = $this->container->get('PlatformService');
            $Platform = $PlatformService->guessByRawString($offer['Platform']);

            $row['platform'] = $Platform;


//var_dump("Ad4games device: ", $row['devices']);
            $data[] = $row;
        }
//        var_dump($data);

        return $data;
    }

}