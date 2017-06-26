<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Enum\Tag;
use AppBundle\Enum\Transport;
use AppBundle\EventSubscriber\SessionHandler;
use AppBundle\EventSubscriber\StaticConfig;
use AppBundle\Helper\Mobile_Detect;
use AppBundle\Helper\Time;
use AppBundle\Model\Activity;
use AppBundle\Model\Area;
use AppBundle\Model\Banner;
use AppBundle\Model\TimeSlot;
use Symfony\Bridge\Twig\AppVariable;
use Symfony\Component\DependencyInjection\ContainerInterface;
use \Twig_Extension;

class CoolExtension extends Twig_Extension
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getName()
    {
        return 'cool_extension';
    }

    public function getFilters() {
        return array(
//            new \Twig_SimpleFilter('price', array($this, 'priceFilter')),
        );
    }

    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('str_replace', array($this, 'str_replace')),
        );
    }

    public function str_replace($search, $replace, $subject, &$count = null)
    {
        return str_replace($search, $replace, $subject, $count);
    }
}
