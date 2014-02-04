<?php
/**
 * Created by PhpStorm.
 * User: Aslan
 * Date: 31.01.14
 * Time: 4:34
 */

namespace Katana\JsonParserBundle\Parser;

use Katana\JsonParserBundle\Parser\BaseParser;


class GlispaParser extends BaseParser{


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
    public function parse( $xml_str )
    {

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xml_str);
        unset($xml_str);
        if (!$xml) {
            echo "Failed loading XML\n";
            foreach(libxml_get_errors() as $error) {
                echo "<br />", $error->message;
            }
        }

        $data = array();

        foreach ($xml->xpath('/data/campaign') as $offer)
        {
            $row = array();

            $row['external_id'] = (int)$offer['glispaID'];
            $row['name'] = $offer['name'];
            $row['payout'] = $this->parsePayout($offer->payout);

            if(isset($offer->creatives[0]->creative->link)){
                $row['preview_url'] = $offer->creatives[0]->creative->link;
            }
            else{
                $row['preview_url'] = '';
            }

            /***
             * Страны
             */
            $row['countries'] = array();

            $country_codes = array_map( "trim", explode(' ', $offer->countries));
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

            $Device = $this->container->get('doctrine')->getRepository('KatanaDictionaryBundle:Device')->findInString($row['name']);

            if(!empty($Device) && is_object($Device)){
                $row['devices'][] = $Device;
            }

            $row['platform'] = null;

            $data[] = $row;
        }

        return $data;
    }



    public function parsePayout($str)
    {
        preg_match( '/\s*(\d+\.\d+)\s+.*/', $str, $matches);

        $payout = 0.0;

        if(isset($matches[1])){
            $payout = floatval($matches[1]);
        }

        return $payout;
    }
} 