Behat Zend Framework Extension
==============================

Zf-behat-extension is an integration layer between Behat 3.0+ and Zend Framework 2/3 and it provides:

- Configuration driven automatic bootstraping of the application
- `ZendBootstrapDictionary` and `ZendBootstrapOverride`, which provide an bootstraped instance of `Zend\Mvc\Application` for your contexts
- Argument resolver to pass services from the container to your contexts
- Additional `zend` driver for Mink (if `MinkExtension` is installed)

Supported platforms
---

- PHP 5.4+
- Behat 3.0+
- Zend Framework 2.x and 3.x

How to install
---

This extension requires:

- Behat 3.0+
- Zend Framework 2.x or 3.x

The recommended installation method is through [Composer](http://getcomposer.org):

```bash
    $ composer require --dev springernature/behat-zf-extension
```

You can then activate the extension in your `behat.yml`:

```yml

        default:
            # ...
            extensions:
                SpringerNature\Behat\ZFExtension: ~
```

Optionally you can activate the Mink driver, in your `behat.yml`:

```yml

        default:
            # ...
            extensions:
                Behat\MinkExtension:
                    default_session: 'zend'
                    sessions:
                        zend:
                            zend: ~
```

Note

Most of the examples in this document show behat being run via ``vendor/bin/behat``,
which is the default location when installing it through Composer.

Licensing
---

This software is available under the [MIT license](LICENSE).

Maintenance
---

Submit issues and PR's to this github.

Usage
---

### Overriding services

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

### Providing application services to context

If you want to pass services instantiated by the application container to your contexts, in `behat.yml`:

```yml
    # ...
    suites:
       default:
          contexts:
            - MyAwesomeContext:
                myservice: 'MyApp\Service\MyService' # this is the key registered in the container
```

In the context:

```php
<?php

namespace behat\MyApp\Context;

class DefaultContext extends \Behat\MinkExtension\Context\MinkContext
{
    public function __construct(\MyApp\Service\MyService $myservice)
    {
       // ...
    }
}
```

### Handling file uploads

Zend has php file upload checker built in that makes it impossible to test file uploads in behat. If you try to inject a file it will give you the following error message:
`File was illegally uploaded. This could be a possible attack`.

To allow the validation to pass in the project `composer.json` file, add to the `autoload-dev` the location to the file. For example:

```json
    "autoload-dev": {
        "files": ["vendor/springernature/behat-zf-extension/src/DisablePhpUploadChecks.php"]
    }
```

After adding the `files` line do a `composer dumpautoload` to load the file.

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


Extra
----

This extension is inspired by the Symfony2extension by [Konstantin Kudryashov](https://github.com/everzet).
