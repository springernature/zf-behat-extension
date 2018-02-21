<?php

namespace SpringerNature\Behat\ZFExtension\Driver;

use Behat\Mink\Driver\BrowserKitDriver;
use Symfony\Component\BrowserKit\Client;

class KernelDriver extends BrowserKitDriver
{
    public function __construct(Client $client, $baseUrl = null)
    {
        parent::__construct($client, $baseUrl);
    }
}
