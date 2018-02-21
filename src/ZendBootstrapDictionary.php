<?php


namespace SpringerNature\Behat\ZFExtension;


trait ZendBootstrapDictionary
{

    public function onBootZf(BootZFEvent $event)
    {
        $this->overrideApplication($event->getApplication());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            BootZFEvent::EVENT_NAME => 'onBootZf',
        ];
    }
}
