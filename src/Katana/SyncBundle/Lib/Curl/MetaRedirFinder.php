<?php
/**
 * Created by PhpStorm.
 * User: Aslan
 * Date: 28.01.14
 * Time: 22:36
 */

namespace Katana\SyncBundle\Lib\Curl;


use Katana\SyncBundle\Lib\Curl\BaseRedirFinder;


class MetaRedirFinder extends BaseRedirFinder {

    public function findUrl()
    {
        if(empty($this->html)){
            return false;
        }

        libxml_use_internal_errors(true);

        $doc = new \DOMDocument;

        $doc->preserveWhiteSpace = false;
        $doc->strictErrorChecking = false;

        $doc->loadHTML($this->html);

        /** Parse Meta Tag */
        $xpath = new \DOMXPath($doc);

        $query = '//meta[@http-equiv="refresh"]';

        $node = $xpath->query($query, $doc);

        if(  $node->length < 1 ||
            !$node->item(0)->hasAttributes() ||
            !is_object($node->item(0)->attributes->getNamedItem('content')) ){
            return false;
        }

        $meta_content = $node->item(0)->attributes->getNamedItem('content')->textContent;


        if(!strlen($meta_content)){
            return false;
        }

        /** Find Url in Meta Tag Attribute - "content" */
        preg_match('/url=(\')?(.*)(?(1)\1|)/i', $meta_content, $matches);


        if(!isset($matches[2]) || !strlen($matches[2])){
            return false;
        }

        return $matches[2];
    }
}