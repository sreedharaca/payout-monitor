<?php
/**
 * Created by PhpStorm.
 * User: Aslan
 * Date: 31.01.14
 * Time: 4:34
 */

namespace Katana\JsonParserBundle\Parser;

use Katana\JsonParserBundle\Parser\BaseParser;


class IquconnectParser extends BaseParser{


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

        //free memory
        $xml_str = null;

        if (!$xml) {
            echo "Failed loading XML\n";
            foreach(libxml_get_errors() as $error) {
                echo "<br />", $error->message;
            }
        }

        $data = array();

        foreach ($xml->offers->offer as $offer)
        {
            $row = array();

            $row['external_id'] = (int)$offer->offer_id;
            $row['name'] = $offer->offer_name;
            $row['payout'] = floatval(substr(trim($offer->payout), 1));

            $row['preview_url'] = $offer->preview_link;

            /***
             * Страны
             */
            $row['countries'] = array();

            $country_codes = array();

            if( isset($offer->allowed_countries->country) )
            {
                foreach ($offer->allowed_countries->country as $country) {
                    $country_codes[] = $country->country_code;
                }

                $country_codes = array_unique($country_codes);

                foreach($country_codes as $country_code)
                {
                    $Country = $this->container->get('doctrine')->getRepository('KatanaDictionaryBundle:Country')->findOneByCode($country_code);

                    if(is_object($Country)){
                        $row['countries'][] = $Country;
                    }
                }
            }

            $row['devices'] = array();

            $row['platform'] = null;

            $data[] = $row;
        }

        return $data;
    }

} 