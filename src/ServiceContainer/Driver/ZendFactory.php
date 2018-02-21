<?php

namespace SpringerNature\Behat\ZFExtension\ServiceContainer\Driver;

use Behat\MinkExtension\ServiceContainer\Driver\DriverFactory;
use SpringerNature\Behat\ZFExtension\Zend\ZendClient;
use SpringerNature\Behat\ZFExtension\Driver\KernelDriver;
use SpringerNature\Behat\ZFExtension\ServiceContainer\ZFExtension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class ZendFactory implements DriverFactory
{

    /**
     * {@inheritdoc}
     */
    public function getDriverName()
    {
        return 'zend';
    }

    /**
     * {@inheritdoc}
     */
    public function supportsJavascript()
    {
        return false;
    }


    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildDriver(array $config)
    {
        if (!class_exists('Behat\Mink\Driver\BrowserKitDriver')) {
            throw new \RuntimeException(
                'Install MinkBrowserKitDriver in order to use the zend driver.'
            );
        }
        return new Definition(KernelDriver::class, array(
            new Reference(ZendClient::class),
            '%mink.base_url%',
        ));
    }
}
