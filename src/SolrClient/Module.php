<?php

namespace SolrClient;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface,
    Zend\ModuleManager\Feature\BootstrapListenerInterface,
    Zend\ModuleManager\Feature\ConfigProviderInterface,
    Zend\ModuleManager\Feature\ServiceProviderInterface,
    Zend\EventManager\EventInterface,
    Zend\Loader\AutoloaderFactory,
    Zend\Loader\StandardAutoloader,
    SolrClient\Service\ClientFactory,
    SolrClient\Service\QueryFactory,
    SolrClient\Service\LoggerCollectorFactory;

/**
 * Main module class
 *
 * @license MIT
 * @author  Bostjan Oblak <bostjan@muha.cc>
 */
class Module implements AutoloaderProviderInterface, BootstrapListenerInterface, ServiceProviderInterface, ConfigProviderInterface {

    /**
     * {@inheritDoc}
     */
    public function getAutoloaderConfig() {
        return array(
            AutoloaderFactory::STANDARD_AUTOLOADER => array(
                StandardAutoloader::LOAD_NS => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig() {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * {@inheritDoc}
     */
    public function onBootstrap(EventInterface $e) {
        $app = $e->getTarget();
        $config = $app->getServiceManager()->get('Config');
        
        if (
                isset($config['zenddevelopertools']['profiler']['enabled'])
                && $config['zenddevelopertools']['profiler']['enabled']
        ) {
            $app->getServiceManager()->get('solr.logger_collector.default');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getServiceConfig() {
        return array(
            'factories' => array(
                'solr.solr_default' => new ClientFactory('solr_default'),
                'solr.query' => new QueryFactory(),
                'solr.logger_collector.default' => new LoggerCollectorFactory('solr_default'),
            ),
        );
    }

}
