<?php

namespace SpringerNature\Behat\ZFExtension;

use Symfony\Component\EventDispatcher\Event;
use Zend\Mvc\Application;

class BootZFEvent extends Event
{
    const EVENT_NAME = 'zend_extension.zend.boot';

    /**
     * @var Application
     */
    private $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

}
