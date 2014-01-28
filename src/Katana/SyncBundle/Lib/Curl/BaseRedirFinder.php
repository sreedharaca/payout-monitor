<?php

namespace Katana\SyncBundle\Lib\Curl;


abstract class BaseRedirFinder {

    protected  $html;

    function setHtml($html)
    {
        $this->html = $html;
    }

    abstract public function findUrl();
}