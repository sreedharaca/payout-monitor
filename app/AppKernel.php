<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{

    public function init()
    {
        date_default_timezone_set('Europe/Moscow');
        parent::init();
    }

    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Katana\OfferBundle\KatanaOfferBundle(),
            new Sensio\Bundle\BuzzBundle\SensioBuzzBundle(),
            new Katana\AffiliateBundle\KatanaAffiliateBundle(),
            new Katana\JsonParserBundle\KatanaJsonParserBundle(),
            new Katana\DictionaryBundle\KatanaDictionaryBundle(),
            new Katana\SyncBundle\KatanaSyncBundle(),
            new Katana\LogBundle\KatanaLogBundle(),
            new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle(),
            new Katana\ImportBundle\KatanaImportBundle(),
            new Katana\StatusBundle\KatanaStatusBundle(),
            new Anchovy\CURLBundle\AnchovyCURLBundle()
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
