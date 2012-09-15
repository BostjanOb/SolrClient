<?php

namespace SolrClient\Logging;

/**
 * Logger interface
 *
 * @license MIT
 * @author  Bostjan Oblak <bostjan@muha.cc>
 */
interface LoggerInterface
{
  
  public function start($query);
  
  public function end();
  
}