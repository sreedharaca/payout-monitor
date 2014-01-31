<?php
/**
 * Created by PhpStorm.
 * User: Aslan
 * Date: 31.01.14
 * Time: 17:51
 */

namespace Katana\SyncBundle\Lib\Curl;

use Katana\SyncBundle\Lib\Curl\BaseRedirFinder;


class NullFinder extends BaseRedirFinder {

    public function findUrl()
    {
        return false;
    }

} 