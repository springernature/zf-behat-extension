<?php

namespace SpringerNature\Behat\ZFExtension\Context\Argument;

use Behat\Behat\Context\Argument\ArgumentResolver;
use ReflectionClass;
use Zend\ServiceManager\ServiceLocatorInterface;

final class ServiceArgumentResolver implements ArgumentResolver
{
    /**
     * @var ServiceLocatorInterface
     */
    private $container;

    public function __construct(ServiceLocatorInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveArguments(ReflectionClass $classReflection, array $arguments)
    {
        $newArguments = [];

        foreach ($arguments as $key => $argument) {
            $newArguments[$key] = $this->resolveArgument($argument);
        }

        return $newArguments;
    }

    /**
     * Resolves single argument using container.
     *
     * @param string $argument
     *
     * @return mixed
     */
    private function resolveArgument($argument)
    {
        if (is_array($argument)) {
            return array_map([$this, 'resolveArgument'], $argument);
        }
        if ( ! is_string($argument)) {
            return $argument;
        }

        if ($this->container->has($argument)) {
            return $this->container->get($argument);
        }

        return $argument;
    }
}

