<?php
namespace SpringerNature\Behat\ZFExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventInitializer implements ContextInitializer
{
    private $eventManager;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventManager = $eventDispatcher;
    }

    public function initializeContext(Context $context)
    {
        if ( ! $context instanceof EventSubscriberInterface) {
            return;
        }

        $this->eventManager->addSubscriber($context);
    }
}
