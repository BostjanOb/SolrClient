<?php

namespace SolrClient\Service;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    SolrClient\Client\Client as SolrClient;

/**
 * Client factory
 *
 * @license MIT
 * @author  Bostjan Oblak <bostjan@muha.cc>
 */
class ClientFactory implements FactoryInterface {
    
    /**
     * @var string
     */
    protected $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }
    
    public function createService(ServiceLocatorInterface $serviceLocator) {
        
        $options = $serviceLocator->get('Configuration');
        $options = $options['solr'];
        
        $connParams = $options['connection'][$this->name];
        
        $httpUri = new \Zend\Uri\Http();
        
        $httpUri->setHost($connParams['host'])
                ->setPath($connParams['path'])
                ->setPort($connParams['port'])
                ->setScheme($connParams['scheme'])
                ->setUser($connParams['user'])
                ->setPassword($connParams['password']);
        
        $client = new SolrClient($httpUri);
        
        $config = $options['configuration'][$this->name];
        $client->setSelectPath($config['select_path'])
                ->setUpdatePath($config['update_path'])
                ->setResultClass($config['resultClass']);
               
        return $client;
    }
    
}