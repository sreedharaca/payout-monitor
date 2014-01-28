<?php
/**
 * Created by PhpStorm.
 * User: Aslan
 * Date: 28.01.14
 * Time: 22:37
 */

namespace Katana\SyncBundle\Lib\Curl;


use Katana\SyncBundle\Lib\Curl\BaseRedirFinder;


class JsRedirFinder extends BaseRedirFinder {

    public function findUrl()
    {
        if(empty($this->html)){
            return false;
        }

        if( preg_match('/window\.location\.replace\(\s*("|\')?(.*)(?(1)\1|)\s*\)/i', $this->html, $value) ||
            preg_match('/window\.location=("|\')?(.*)(?(1)\1|)/i', $this->html, $value) ){

            return $value[2];
        }

        return false;
    }
}