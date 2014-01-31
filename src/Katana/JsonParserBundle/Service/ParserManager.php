<?php

namespace Katana\JsonParserBundle\Service;

use Katana\AffiliateBundle\Lib\AffiliateDictionary;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Katana\JsonParserBundle\Parser\HasoffersBaseJsonParser;
use Katana\JsonParserBundle\Parser\Hasoffers2BaseJsonParser;
use Katana\JsonParserBundle\Parser\Ad4gameJsonParser;
use Katana\JsonParserBundle\Parser\ComboappJsonParser;
use Katana\JsonParserBundle\Parser\MobilePartnerJsonParser;
use Katana\JsonParserBundle\Parser\GlispaParser;
use Katana\JsonParserBundle\Parser\ClicksmobParser;
use Katana\JsonParserBundle\Parser\IquconnectParser;
use Katana\JsonParserBundle\Parser\ClickkyParser;

class ParserManager extends AffiliateDictionary
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getParser($provider)
    {
        switch( $provider )
        {
            case self::PROVIDER_KATANA:
                return new HasoffersBaseJsonParser($this->container);
                break;

            case self::PROVIDER_AD4GAME:
                return new Ad4gameJsonParser($this->container);
                break;

            case self::PROVIDER_UNICUME:
                return new HasoffersBaseJsonParser($this->container);
                break;

            case self::PROVIDER_YEAHMOBI:
                return new HasoffersBaseJsonParser($this->container);
                break;

            case self::PROVIDER_COMBOAPP:
                return new ComboappJsonParser($this->container);
                break;

            case self::PROVIDER_KISSMYADS:
                return new HasoffersBaseJsonParser($this->container);
                break;

            case self::PROVIDER_TAPGERINE:
                return new Hasoffers2BaseJsonParser($this->container);
                break;

            case self::PROVIDER_ICONPEAK:
                return new HasoffersBaseJsonParser($this->container);
                break;

            case self::PROVIDER_ADSUP:
                return new HasoffersBaseJsonParser($this->container);
                break;

            case self::PROVIDER_MOBPARTNER:
                return new MobilePartnerJsonParser($this->container);
                break;

            case self::PROVIDER_GLISPA:
                return new GlispaParser($this->container);
                break;

            case self::PROVIDER_CLICKROCKET:
                return new HasoffersBaseJsonParser($this->container);
                break;

            case self::PROVIDER_CLICKSMOB:
                return new ClicksmobParser($this->container);
                break;

            case self::PROVIDER_IQUCONNECT:
                return new IquconnectParser($this->container);
                break;

            case self::PROVIDER_MOTIVE:
                return new IquconnectParser($this->container);
                break;

            case self::PROVIDER_CLICKKY:
                return new ClickkyParser($this->container);
                break;

            default:
                throw new \Exception("Requested unknown Affiliate Parser: $provider");
        }
    }
}