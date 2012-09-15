<?php

namespace SolrClient\Logging;

/**
 * Debug stack for logging
 *
 * @license MIT
 * @author  Bostjan Oblak <bostjan@muha.cc>
 */
class DebugStack implements LoggerInterface {

    public $queries = array();
    private $_currentQuery = 0;
    private $_start;
    private $_totalElapsed = 0;

    public function end() {
        $this->queries[$this->_currentQuery]['executionMS'] = microtime(true) - $this->_start;

        $this->_totalElapsed += $this->queries[$this->_currentQuery]['executionMS'];
    }

    public function start($query) {
        $this->_start = microtime(true);
        $this->queries[++$this->_currentQuery] = array('query' => $query, 'executionMS' => 0);
    }

    public function getTotalElapsedMs() {
        return $this->_totalElapsed;
    }

    public function getNumberOfQueries() {
        return $this->_currentQuery;
    }

}