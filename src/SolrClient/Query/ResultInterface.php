<?php

namespace SolrClient\Query;

use Zend\Http\Response;

/**
 * Solr Result Interface
 *
 * @license MIT
 * @author  Bostjan Oblak <bostjan@muha.cc>
 */
interface ResultInterface {

    public function __construct(Response $response);
}

