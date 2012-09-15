<?php

namespace SolrClient\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use RuntimeException;
use SolrClient\Collector\SolrLoggerCollector;
use SolrClient\Logging\DebugStack;

/**
 * Logger Collector factory
 *
 * @license MIT
 * @author  Bostjan Oblak <bostjan@muha.cc>
 */
class LoggerCollectorFactory implements FactoryInterface {

    /**
     * @var string
     */
    protected $name;

    /**
     * @param $name
     */
    public function __construct($name) {
        $this->name = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator) {
        /** @var $options \DoctrineORMModule\Options\SQLLoggerCollectorOptions */
        $options = $serviceLocator->get('Config');
        $options = $options['solr'];
        $options = isset($options['logger_collector'][$this->name]) ? $options['logger_collector'][$this->name] : null;

        if (null === $options) {
            throw new RuntimeException(sprintf(
                            'Configuration with name "%s" could not be found in "solr.logger_collector".', $this->name
            ));
        }

        $debugStack = new DebugStack();
        /* @var $client \SolrClient\Client\Client */
        $client = $serviceLocator->get($options);
        $client->setLogger($debugStack);

        return new SolrLoggerCollector($debugStack, $this->name);
    }

}
