<?php

namespace Katana\JsonParserBundle\Parser;

use Katana\JsonParserBundle\Parser\BaseParser;

class Hasoffers2BaseJsonParser extends BaseParser
{

    /***
     * @param null $json
     * @return array
     */
    public function parse( $json = null )
    {

        $array = json_decode($json, true);

        $data = array();
        foreach($array['response']['data'] as $id => $object)
        {
            $offer = $object['Offer'];

            $row = array();
            $row['external_id'] = (int)$offer['id'];
            $row['name'] = strval($offer['name']);
            $row['payout'] = floatval($offer['default_payout']);
            $row['preview_url'] = $offer['preview_url'];
//            $row['json'] = json_encode($offer);

            /***
             * Страны
             */

            /** нет стран у этого партнера */
            $row['countries'] = array();

            /*            $country_codes = array_map( "trim", explode(',', $offer['countries_short']));
                        foreach($country_codes as $country_code)
                        {
                            $Country = $this->container->get('doctrine')->getRepository('KatanaDictionaryBundle:Country')->findOneByCode($country_code);

                            if(is_object($Country)){
                                $row['countries'][] = $Country;
                            }
                        }*/

            /***
             * Девайсы
             */
            $row['devices'] = array();

            /* $devices = array_map( "trim", explode(',', $offer['categories']));
             foreach($devices as $device_name)
             {
                 $Device = $this->container->get('doctrine')->getRepository('KatanaDictionaryBundle:Device')->findInString($device_name);

                 if(!empty($Device) && is_object($Device)){
                     $row['devices'][] = $Device;
                 }
             }*/

            /***
             * Платформа
             */
            $PlatformService = $this->container->get('PlatformService');
            $Platform = $PlatformService->guessByRawString($offer['name']);

            $row['platform'] = $Platform;


            $data[] = $row;
        }

        return $data;
    }


}