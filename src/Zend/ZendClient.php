<?php
namespace SpringerNature\Behat\ZFExtension\Zend;

use Behat\Behat\EventDispatcher\Event\StepTested;
use SpringerNature\Behat\ZFExtension\BootZFEvent;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\BrowserKit\History;
use Symfony\Component\BrowserKit\Request as DomRequest;
use Symfony\Component\BrowserKit\Response as DomResponse;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zend\Http\PhpEnvironment\Request as ZendRequest;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response as ZendResponse;
use Zend\Http\PhpEnvironment\Response;
use Zend\Http\Response\Stream;
use Zend\Mvc\Application;
use Zend\Stdlib\Parameters;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service;

class ZendClient extends Client implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $bootstrapFile;

    /**
     * @var array
     */
    private $applicationConfig;

    /**
     * @var string
     */
    private $content;

    private $eventDispatcher;

    public function __construct(
        array $applicationConfig,
        $bootstrapFile = null,
        EventDispatcher $eventDispatcher,
        array $server = array(),
        History $history = null,
        CookieJar $cookieJar = null
    ) {
        parent::__construct($server, $history, $cookieJar);

        $this->bootstrapFile = $bootstrapFile;
        $this->applicationConfig = $applicationConfig;
        $this->eventDispatcher = $eventDispatcher;
    }


    /**
     * {@inheritdoc}
     */
    protected function doRequest($request)
    {
        $serviceManagerConfig = new Service\ServiceManagerConfig([]);
        $serviceManager = new ServiceManager();
        $serviceManagerConfig->configureServiceManager($serviceManager);
        $serviceManager->setService('ApplicationConfig', $this->applicationConfig);
        $serviceManager->get('ModuleManager')->loadModules();

        $listenersFromAppConfig     = isset($configuration['listeners']) ? $configuration['listeners'] : [];
        $config                     = $serviceManager->get('Config');
        $listenersFromConfigService = isset($config['listeners']) ? $config['listeners'] : [];

        $listeners = array_unique(array_merge($listenersFromConfigService, $listenersFromAppConfig));

        $application = $serviceManager->get('Application');

        if ($this->bootstrapFile != null) {
            if ( ! is_readable($this->bootstrapFile)) {
                throw new \RuntimeException(
                    sprintf('Bootstrap file is not accessible (%s)', $this->bootstrapFile)
                );
            }

            require $this->bootstrapFile;
        }

        $event = new BootZFEvent($application);
        $this->eventDispatcher->dispatch(BootZFEvent::EVENT_NAME, $event);
        $application->bootstrap($listeners);

        $event = $application->getMvcEvent();
        $event->setRequest($request);

        ob_start();
        $application->run();

        $this->content = ob_get_contents();
        ob_clean();

        return $event->getResponse();
    }

    /**
     * @param DomRequest $request
     * @return ZendRequest
     */
    protected function filterRequest(DomRequest $request)
    {
        $httpRequest = new ZendRequest();

        $httpRequest->setServer(new Parameters($request->getServer()));
        $httpRequest->setUri($request->getUri());
        $httpRequest->setRequestUri($httpRequest->getUri()->getPath());
        $httpRequest->setMethod($request->getMethod());
        $httpRequest->setPost(new Parameters($request->getParameters()));
        $httpRequest->setQuery(new Parameters($this->parseGetParams($request->getUri())));
        $httpRequest->setContent($request->getContent());
        $httpRequest->setCookies(new Parameters($request->getCookies()));
        $httpRequest->setFiles(new Parameters($request->getFiles()));

        return $httpRequest;
    }

    protected function filterResponse($response)
    {
        $body = $response->getBody();
        if ($response instanceof Stream) {
            $body = $this->content;
        }
        /** @var ZendResponse $response */
        return new DomResponse($body, $response->getStatusCode(), $response->getHeaders()->toArray());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [];
    }

    protected function parseGetParams($uri)
    {
        parse_str(parse_url($uri, PHP_URL_QUERY), $params);

        return $params;
    }
}
