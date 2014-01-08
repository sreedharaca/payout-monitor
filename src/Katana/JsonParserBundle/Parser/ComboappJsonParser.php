<?php

namespace Katana\JsonParserBundle\Parser;

use Katana\JsonParserBundle\Parser\BaseJsonParser;

class ComboappJsonParser extends BaseJsonParser
{

    //private $json = '"532":{"Offer":{"id":"532","name":"BINGO Blitz - iPhone\/iPad US\/CA\/AU - non incent","description":"","require_approval":"0","require_terms_and_conditions":0,"terms_and_conditions":null,"preview_url":"https:\/\/itunes.apple.com\/us\/app\/bingo-blitz-free-bingo-+-slots\/id529996768?mt=8","currency":null,"default_payout":"2.00","status":"active","expiration_date":"2014-10-31","payout_type":"cpa_flat","percent_payout":null,"featured":null,"allow_website_links":"0","allow_direct_links":"0","show_custom_variables":"0","show_mail_list":"0","dne_list_id":"0","email_instructions":"0","email_instructions_from":"","email_instructions_subject":"","use_target_rules":"0","is_expired":"0","dne_download_url":null,"dne_unsubscribe_url":null}}';
    /**
     * @var string
     */
    private $url = 'http://api.hasoffers.com/v3/Affiliate_Offer.json?Method=findAll&api_key=5ebdb288236af60639bac9df932a903806fdb645232865471695766dd311789a&NetworkId=comboapp';

    /***
     * @param null $json
     * @return array
     */
    public function parse( $json = null )
    {
        echo "Comboapp parser executed\n";


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
            $row['json'] = json_encode($offer);

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