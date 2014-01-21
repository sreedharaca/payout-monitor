<?php

namespace Katana\JsonParserBundle\Service;

use Katana\JsonParserBundle\Parser\HasoffersBaseJsonParser;
use Katana\JsonParserBundle\Parser\Hasoffers2BaseJsonParser;
use Katana\JsonParserBundle\Parser\YeahmobiJsonParser;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Katana\JsonParserBundle\Parser\KatanaJsonParser;
use Katana\JsonParserBundle\Parser\Ad4gameJsonParser;
use Katana\JsonParserBundle\Parser\UnicumeJsonParser;
use Katana\JsonParserBundle\Parser\ComboappJsonParser;


class JsonParserManager
{
    const PROVIDER_ADSUP     = 'Adsup';
    const PROVIDER_KATANA    = 'Katana';
    const PROVIDER_AD4GAME   = 'Ad4game';
    const PROVIDER_UNICUME   = 'Unicume';
    const PROVIDER_YEAHMOBI  = 'Yeahmobi';
    const PROVIDER_COMBOAPP  = 'Comboapp';
    const PROVIDER_ICONPEAK  = 'Iconpeak';
    const PROVIDER_KISSMYADS = 'Kissmyads';
    const PROVIDER_TAPGERINE = 'Tapgerine';

    //TODO добавить все сетки

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

            default:
                return null;
        }
    }
}