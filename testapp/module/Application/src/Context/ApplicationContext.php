<?php

namespace Application\Context;

use Behat\Behat\Context\Context;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\ServiceManager;

class ApplicationContext implements Context
{

    private $controllerManager;

    public function __construct(ControllerManager $manager = null)
    {
        $this->controllerManager = $manager;
    }


    /**
     * @Then /^my context should have been constructed with dependencies$/
     */
    public function myContextShouldHaveBeenConstructedWithDependencies()
    {
        if ( ! $this->controllerManager instanceof ControllerManager) {
            throw new \RuntimeException('Constructor was not provided dependencies');
        }
    }

    /**
     * @Then /^my bootstrap file should have been ran$/
     */
    public function myBootstrapFileShouldHaveBeenRan()
    {
        if (! isset($GLOBALS['bootstrap_ran']) || $GLOBALS['bootstrap_ran'] !== true) {
            throw new \RuntimeException('The bootstrap file have not been run');
        }
    }
}
