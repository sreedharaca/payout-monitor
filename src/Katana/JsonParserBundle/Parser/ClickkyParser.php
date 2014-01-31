<?php

namespace Katana\JsonParserBundle\Parser;

use Katana\JsonParserBundle\Parser\BaseParser;

class ClickkyParser extends BaseParser
{

    public function parse( $json = null )
    {
//        if(!$json){ $json = $this->json; }

        $array = json_decode($json, true);

//        var_dump($array['data']);

        $data = array();
        foreach($array['offers'] as $offer)
        {
            $row = array();
            $row['external_id'] = (int)$offer['offer_id'];
            $row['name']        = $offer['name'];
            $row['payout']      = sprintf("%.2f", floatval($offer['payout']));
            $row['preview_url'] = $offer['link'];
            /***
             * Страны
             */
            $row['countries'] = array();

            $country_codes = $offer['targeting']['countries'];
            foreach($country_codes as $country_code)
            {
                $Country = $this->container->get('doctrine')->getRepository('KatanaDictionaryBundle:Country')->findOneByCode($country_code);

                if(is_object($Country)){
                    $row['countries'][] = $Country;
                }
            }

            /***
             * Девайсы
             */
            $row['devices'] = array();

            $devices = $offer['targeting']['os'];
            foreach($devices as $device_name)
            {
                $Device = $this->container->get('doctrine')->getRepository('KatanaDictionaryBundle:Device')->findInString($device_name);

                if(!empty($Device) && is_object($Device)){
                    $row['devices'][] = $Device;
                }
            }

            $row['platform'] = null;

            $data[] = $row;
        }
//        var_dump($data);

        return $data;
    }


}