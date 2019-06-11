<?php

namespace HeimrichHannot\GoogleMapsListBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Config\ConfigPluginInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use HeimrichHannot\GoogleMapsListBundle\ContaoGoogleMapsListBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class Plugin implements BundlePluginInterface, RoutingPluginInterface, ConfigPluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        $loadAfter = [
            ContaoCoreBundle::class
        ];

        return [
            BundleConfig::create(ContaoGoogleMapsListBundle::class)->setLoadAfter($loadAfter)
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel)
    {
//        return $resolver->resolve(__DIR__.'/../Resources/config/routing.yml')->load(__DIR__.'/../Resources/config/routing.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader, array $managerConfig)
    {
//        $loader->load('@ContaoGoogleMapsListBundle/Resources/config/services.yml');
//        $loader->load('@ContaoGoogleMapsListBundle/Resources/config/datacontainers.yml');
    }
}
