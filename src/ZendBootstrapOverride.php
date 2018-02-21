<?php


namespace SpringerNature\Behat\ZFExtension;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zend\Mvc\Application;

interface ZendBootstrapOverride extends EventSubscriberInterface
{
    public function overrideApplication(Application $application);
}
