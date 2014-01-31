<?php
/**
 * Created by PhpStorm.
 * User: Aslan
 * Date: 29.01.14
 * Time: 20:26
 */

namespace Katana\SyncBundle\Service;

use Katana\AffiliateBundle\Entity\Affiliate;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Katana\SyncBundle\Lib\AffiliateAuth\ClicksmobAuthenticator;

class AffiliateAuthManager {

    private $container;

    const PROVIDER_CLICKSMOB = 'Clicksmob';

    public function __construct(Container $container)
    {
        $this->container = $container;
    }


    public function getAffiliateAuthenticator(Affiliate $Affiliate)
    {
        switch($Affiliate->getName())
        {
            case self::PROVIDER_CLICKSMOB:
                return new ClicksmobAuthenticator($this->container);
                break;

            default:
                throw new \Exception('Unexistent Affiliate. Probably typo.');
        }
    }

}