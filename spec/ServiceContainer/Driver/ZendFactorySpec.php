<?php

namespace spec\SpringerNature\Behat\ZFExtension\ServiceContainer\Driver;

use SpringerNature\Behat\ZFExtension\ServiceContainer\Driver\ZendFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SpringerNature\Behat\ZFExtension\Zend\ZendClient;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class ZendFactorySpec extends ObjectBehavior
{
    function it_is_a_driver_factory()
    {
        $this->shouldHaveType('Behat\MinkExtension\ServiceContainer\Driver\DriverFactory');
    }

    function it_is_named_zend()
    {
        $this->getDriverName()->shouldReturn('zend');
    }

    function it_does_not_support_javascript()
    {
        $this->supportsJavascript()->shouldBe(false);
    }

    function it_does_not_have_any_specific_configuration(ArrayNodeDefinition $builder)
    {
        $this->configure($builder);
    }

    function it_creates_a_kernel_driver_definition()
    {
        $definition = $this->buildDriver(array());
        $definition->shouldBeAnInstanceOf('Symfony\Component\DependencyInjection\Definition');
        $definition->getClass()->shouldBe('SpringerNature\Behat\ZFExtension\Driver\KernelDriver');
        $args = $definition->getArguments();
        $args[0]->shouldBeAnInstanceOf('Symfony\Component\DependencyInjection\Reference');
        $args[0]->__toString()->shouldBe('springernature\behat\zfextension\zend\zendclient');
    }}
