<?php
/**
 * Created by PhpStorm.
 * User: Aslan
 * Date: 31.01.14
 * Time: 4:34
 */

namespace Katana\JsonParserBundle\Parser;

use Katana\JsonParserBundle\Parser\BaseParser;


class ClicksmobParser extends BaseParser {


    /***
     * Parses content and fetches info:
     *
     * external_id
     * name
     * payout
     * preview_url
     * countries
     * devices
     * ~platform
     */
    public function parse( $json_str )
    {
        $json = json_decode($json_str, true);

        $data = [];

        foreach ($json as $offer)
        {
            $row = [];

            $row['external_id'] = (int)$offer['id'];
            $row['name'] = $offer['name'];

            /***
             * Payout
             */
            //берем максимальную из всех ставок
            $payouts = [];
            foreach($offer['userPayouts'] as $payout_arr)
            {
                $payouts[] = floatval($payout_arr['payout']);
            }
            $row['payout'] = max($payouts);


            $row['preview_url'] = $this->findPreviewUrl($offer['targets']);

            /***
             * Страны
             */
            $row['countries'] = [];

            $country_names = [];
            foreach ($offer['userPayouts'] as $payout_arr) {
                $country_names = array_merge($country_names, array_map("trim", explode(',', $payout_arr['countryNames'])));
            }

            $country_names = array_unique($country_names);

            foreach($country_names as $country_str)
            {
                $Country = $this->container->get('doctrine')->getRepository('KatanaDictionaryBundle:Country')->findOneByName($country_str);

                if(is_object($Country)){
                    $row['countries'][] = $Country;
                }
            }

            /***
             * Девайсы
             */
            $row['devices'] = [];

            $row['platform'] = null;

            $data[] = $row;
        }

        return $data;
    }

    private function findPreviewUrl(array $targets)
    {
        foreach ($targets as $target) {

            if(strlen($target['iphonePreviewUrl'])){
                return $target['iphonePreviewUrl'];
            }

            if(strlen($target['ipadPreviewUrl'])){
                return $target['ipadPreviewUrl'];
            }

            if(strlen($target['androidPreviewUrl'])){
                return $target['androidPreviewUrl'];
            }
        }

        return '';
    }

}