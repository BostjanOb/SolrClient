<?php

namespace Solr\Service;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    SolrClient\Query\Query;

/**
 * Client factory
 *
 * @license MIT
 * @author  Volkan Altan <volkanaltan@gmail.com>
 */
class QueryFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $query = new Query();
               
        return $query;
    }
    
}