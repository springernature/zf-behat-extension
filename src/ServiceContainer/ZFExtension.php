<?php

namespace SpringerNature\Behat\ZFExtension\ServiceContainer;

use Behat\Behat\Context\Exception\ContextException;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Behat\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use SpringerNature\Behat\ZFExtension\Context\Initializer\EventInitializer;
use SpringerNature\Behat\ZFExtension\Zend\ZendClient;
use SpringerNature\Behat\ZFExtension\Context\Argument\ServiceArgumentResolver;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use SpringerNature\Behat\ZFExtension\ServiceContainer\Driver\ZendFactory;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Zend\EventManager\EventManager;
use Zend\ModuleManager\Listener\DefaultListenerAggregate;
use Zend\ServiceManager\ServiceManager;

use Zend\Http\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\Console\Console;

class ZFExtension implements ExtensionInterface
{
    const APPLICATION_CONFIG_ID = 'zend_extension.zend.application_config';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // TODO: Implement process() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'zend';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
        if (null !== $minkExtension = $extensionManager->getExtension('mink')) {
            $minkExtension->registerDriverFactory(new ZendFactory());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('application_config_path')->defaultValue('config/application.config.php')->end()
                ->scalarNode('bootstrap')->defaultNull()->end()
            ->end()
        ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $this->loadServiceManager($container, $config['application_config_path']);
        $this->loadDriver($container, $config);
        $this->loadServiceArgumentResolver($container);
        $this->loadEventInitializer($container);
    }

    private function loadServiceManager(ContainerBuilder $container, $appConfigPath)
    {
        if (class_exists(Console::class)) {
            Console::overrideIsConsole(false);
        }
        $serviceManagerConfig = new ServiceManagerConfig();
        $serviceManager = new ServiceManager();

        $serviceManagerConfig->configureServiceManager($serviceManager);

        if ( ! is_readable($appConfigPath)) {
            throw new \RuntimeException(
                sprintf('Cannot load application config (%s), file not accessible', $appConfigPath)
            );
        }
        $appConfig = require $appConfigPath;
        $serviceManager->setService('ApplicationConfig', $appConfig);
        $serviceManager->get('ModuleManager')->loadModules();

        $container->set(ServiceManager::class, $serviceManager);
        $container->set(self::APPLICATION_CONFIG_ID, $appConfig);

    }

    private function loadDriver(ContainerBuilder $container, array $config)
    {
        if ( ! isset($config['bootstrap'])) {
            $config['bootstrap'] = null;
        }
        $definition = new Definition(ZendClient::class, [
            new Reference(self::APPLICATION_CONFIG_ID),
            $config['bootstrap'],
            new Reference(EventDispatcherExtension::DISPATCHER_ID)
        ]);
        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, array('priority' => 0));

        $container->setDefinition(ZendClient::class, $definition);
    }

    private function loadServiceArgumentResolver(ContainerBuilder $container)
    {
        $definition = new Definition(ServiceArgumentResolver::class, [
            new Reference(ServiceManager::class),
        ]);
        $definition->addTag(ContextExtension::ARGUMENT_RESOLVER_TAG, array('priority' => 0));
        $container->setDefinition('zend_extension.context_argument.service_resolver', $definition);
    }

    private function loadEventInitializer(ContainerBuilder $container)
    {

        $definition = new Definition(
            EventInitializer::class,
            [
                new Reference(EventDispatcherExtension::DISPATCHER_ID)
            ]
        );
        $definition->addTag(ContextExtension::INITIALIZER_TAG, ['priority' => 0]);
        $container->setDefinition('zend_extension.zend.event_initializer', $definition);
    }


}
