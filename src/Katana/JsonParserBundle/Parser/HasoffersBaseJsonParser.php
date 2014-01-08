<?php

namespace Katana\JsonParserBundle\Parser;

use Katana\JsonParserBundle\Parser\BaseJsonParser;

class HasoffersBaseJsonParser extends BaseJsonParser
{

    /***
     * @param null $json
     * @return array
     */
    public function parse( $json = null )
    {
        echo "Hasoffers base parser executed\n";

        if(!$json){ $json = $this->json; }

        $array = json_decode($json, true);

//        var_dump($array['data']);

        $data = array();
        foreach($array['data']['offers'] as $offer)
        {
            $row = array();
            $row['external_id'] = (int)$offer['id'];
            $row['name'] = strval($offer['name']);
            $row['payout'] = floatval($offer['payout']);
            $row['preview_url'] = $offer['preview_url'];
            $row['json'] = json_encode($offer);
            /***
             * Страны
             */
            $row['countries'] = array();

            $country_codes = array_map( "trim", explode(',', $offer['countries_short']));
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

            $devices = array_map( "trim", explode(',', $offer['categories']));
            foreach($devices as $device_name)
            {
                $Device = $this->container->get('doctrine')->getRepository('KatanaDictionaryBundle:Device')->findInString($device_name);

                if(!empty($Device) && is_object($Device)){
                    $row['devices'][] = $Device;
                }
            }

            /***
             * Платформа
             */
            $PlatformService = $this->container->get('PlatformService');
            $Platform = $PlatformService->guessByRawString($offer['categories']);

            $row['platform'] = $Platform;

            $data[] = $row;
        }
//        var_dump($data);

        return $data;
    }


}