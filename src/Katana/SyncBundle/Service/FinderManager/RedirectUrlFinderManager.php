<?php
/**
 * Created by PhpStorm.
 * User: Aslan
 * Date: 31.01.14
 * Time: 17:35
 */

namespace Katana\SyncBundle\Service\FinderManager;

use Katana\AffiliateBundle\Lib\AffiliateDictionary;
use Katana\SyncBundle\Lib\Curl\NullFinder;
use Katana\SyncBundle\Lib\Curl\MetaRedirFinder;
use Katana\SyncBundle\Lib\Curl\ClickkyJsRedirUrlFinder;


class RedirectUrlFinderManager extends AffiliateDictionary {

    public function getFinder($Affiliate)
    {
        switch($Affiliate->getName())
        {
            case self::PROVIDER_CLICKKY:
                return new ClickkyJsRedirUrlFinder();
                break;

            default:
                return new MetaRedirFinder();
                break;
        }
    }


}