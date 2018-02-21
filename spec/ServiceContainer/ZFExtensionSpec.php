<?php

namespace spec\SpringerNature\Behat\ZFExtension\ServiceContainer;

use SpringerNature\Behat\ZFExtension\ServiceContainer\ZFExtension;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;

class ZFExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ZFExtension::class);
    }

    function it_is_an_extension()
    {
        $this->shouldImplement(ExtensionInterface::class);
    }
}
