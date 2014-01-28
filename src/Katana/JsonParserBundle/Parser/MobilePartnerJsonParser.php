<?php

namespace Katana\JsonParserBundle\Parser;

use Katana\JsonParserBundle\Parser\BaseJsonParser;

class MobilePartnerJsonParser extends BaseJsonParser
{

    public function parse( $json = null )
    {
        echo "Mobile Partner base parser executed\n";

        if(!$json){ $json = $this->json; }

        $array = json_decode($json, true);

//        var_dump($array['data']);

        $data = array();
        foreach($array['service']['campaigns']['campaign'] as $offer)
        {
            $row = array();
            $row['external_id'] = (int)$offer['id'];
            $row['name'] = strval($offer['name']);
            $row['payout'] = floatval(0.00);
            $row['preview_url'] = $offer['click'];
            $row['json'] = json_encode($offer);

            $row['devices'] = array();
            $row['countries'] = array();
            $row['platform'] = false;

            $data[] = $row;
        }
//        var_dump($data);

        return $data;
    }

}