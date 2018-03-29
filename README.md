Behat Zend Framework Extension
==============================

This project is a Behat extension that integrates the ZendFramework client similar to the Symfony integration.

To accomplish this, we have implemented a ZendFramework client that behaves `BrowserKit` client and exposing the `Zend\Mvc\Application` in an effort to make overriding services easier.


Configuration
-------------

A typical configuration would look like this (`behat.yml.dist`)

```yaml
extensions:
  SpringerNature\Behat\ZFExtension:
    application_config_path: path/to/custom/behat.config.php
  Behat\MinkExtension:
    default_session: 'zend'
    sessions:
      zend:
        zend: ~
```

Because the extension bootstrap a ZF application, you can specify an application config using the `application_config_path` option as the example above. Most of the time this file will include the required modules to boot the app correctly (like the `application.config.php`).

It is also possible to have an external bootstrap file by using the option `bootstrap`.


Overriding services
-------------------

Normally to have control in the tests you would like to override services or other setting for the application.

This extension will expose and allow overriding the application as follows.

```php
<?php

namespace behat\MyApp\Context;

class DefaultContext 
    extends \Behat\MinkExtension\Context\MinkContext 
    implements \SpringerNature\Behat\ZFExtension\ZendBootstrapOverride
{
    use \SpringerNature\Behat\ZFExtension\ZendBootstrapDictionary;

    public function overrideApplication(\Zend\Mvc\Application $application)
    {
        $serviceManager = $application->getServiceManager();
        
        $overridenService = new MyService();
        $overridenService->doSomethingDifferent(true);
        
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('my_service', $overridenService);
        $serviceManager->setAllowOverride(false);
    }
}
```
