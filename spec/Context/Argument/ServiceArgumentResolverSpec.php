<?php

namespace spec\SpringerNature\Behat\ZFExtension\Context\Argument;

use Behat\Behat\Context\Argument\ArgumentResolver;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use ReflectionClass;
use Zend\ServiceManager\ServiceLocatorInterface;

class ServiceArgumentResolverSpec extends ObjectBehavior
{
    function let(ServiceLocatorInterface $container)
    {
        $this->beConstructedWith($container);
    }

    function it_is_an_argument_resolver()
    {
        $this->shouldImplement(ArgumentResolver::class);
    }

    function it_resolves_plain_string_arguments(
        ReflectionClass $reflectionClass,
        ServiceLocatorInterface $container
    ) {
        $service = new \stdClass();
        $container->has('my_service')->willReturn(true);
        $container->get('my_service')->willReturn($service);
        $this->resolveArguments($reflectionClass, array('service' => 'my_service'))->shouldReturn(
            array('service' => $service)
        );
    }

    function it_does_not_resolve_string_arguments_for_not_registered_service(
        ReflectionClass $reflectionClass,
        ServiceLocatorInterface $container
    ) {
        $service = new \stdClass();
        $container->has('my_service')->willReturn(false);
        $container->get('my_service')->shouldNotBeCalled();
        $this->resolveArguments($reflectionClass, array('service' => 'my_service'))->shouldReturn(
            array('service' => 'my_service')
        );
    }

    function it_does_not_try_and_parse_arrays(ReflectionClass $reflectionClass)
    {
        $this->resolveArguments($reflectionClass, array('array' => array(1,2,3)))->shouldReturn(
            array('array' => array(1,2,3))
        );
    }
    function it_resolves_arrays_of_strings_starting_with_at_sign(
        ReflectionClass $reflectionClass,
        ServiceLocatorInterface $container
    ) {
        $container->has('service1')->willReturn(true);
        $container->has('service2')->willReturn(true);
        $container->get('service1')->willReturn($service1 = new \stdClass());
        $container->get('service2')->willReturn($service2 = new \stdClass());
        $this->resolveArguments($reflectionClass, array('array' => array('service1', 'service2')))->shouldReturn(
            array('array' => array($service1, $service2))
        );
    }
}
