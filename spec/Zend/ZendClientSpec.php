<?php

namespace spec\SpringerNature\Behat\ZFExtension\Zend;

use SpringerNature\Behat\ZFExtension\Zend\ZendClient;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Zend\Mvc\ApplicationInterface;

class ZendClientSpec extends ObjectBehavior
{
    function let(EventDispatcher $eventDispatcher)
    {
        $appConfig = [
            'modules' => [],
        ];
        $this->beConstructedWith($appConfig, null, $eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ZendClient::class);
    }

    function it_extends_browser_client()
    {
        $this->shouldHaveType(Client::class);
    }
}
