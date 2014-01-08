<?php
namespace Katana\OfferBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;


class DateToTextExtension extends \Twig_Extension
{
    private $container;

    public function __construct(Container $container){
        $this->container = $container;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('datetotext', array($this, 'dateToTextFilter')),
        );
    }
    
    public function dateToTextFilter($date, $mode=0)
    {
        $DateToTextService = $this->container->get('DateToTextService');

        return $DateToTextService->dateToText($date, $mode=0);
    }

    public function getName()
    {
        return 'datetotext_extension';
    }
}
?>
