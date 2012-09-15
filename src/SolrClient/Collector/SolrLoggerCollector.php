<?php

namespace SolrClient\Collector;

use ZendDeveloperTools\Collector\CollectorInterface;
use ZendDeveloperTools\Collector\AutoHideInterface;
use Zend\Mvc\MvcEvent;
use SolrClient\Logging\DebugStack;

/**
 * Collector to be used in ZendDeveloperTools to record and display queries
 *
 * @license MIT
 * @author  Bostjan Oblak <bostjan@muha.cc>
 */
class SolrLoggerCollector implements CollectorInterface, AutoHideInterface {
    
    /**
     * Collector priority
     */
    const PRIORITY = 10;

    /**
     * @var DebugStack
     */
    protected $logger;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param DebugStack $sqlLogger
     * @param string     $name
     */
    public function __construct(DebugStack $logger, $name) {      
        $this->logger = $logger;
        $this->name = (string) $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getName() {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getPriority() {
        return static::PRIORITY;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(MvcEvent $mvcEvent) {
        
    }

    /**
     * {@inheritDoc}
     */
    public function canHide() {
        return empty($this->logger->queries);
    }

    /**
     * @return int
     */
    public function getQueryCount() {
        return count($this->logger->queries);
    }

    /**
     * @return array
     */
    public function getQueries() {
        return $this->logger->queries;
    }

    /**
     * @return float
     */
    public function getQueryTime() {
        return $this->logger->getTotalElapsedMs();
    }

}
