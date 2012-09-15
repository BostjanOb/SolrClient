<?php

namespace SolrClient\Query;

use Zend\Http\Response;

/**
 * Solr Result
 *
 * @license MIT
 * @author  Bostjan Oblak <bostjan@muha.cc>
 */
class Result
{

  /**
   * @var Response $response
   */
  private $response;

  /**
   * @var array $documents
   */
  private $documents = array();

  /**
   * @var int $numFound
   */
  private $numFound;

  /**
   * Is json parsed
   *
   * @var bool $isParsed
   */
  private $isParsed = false;

  /**
   * Parsed facet fields
   *
   * @var array $facetFields
   */
  private $facetFields = array();

  /**
   * Spellcheck
   *
   * @var array $spellcheck
   */
  private $spellcheck = array();

  /**
   * Spellcheck collation
   *
   * @var string
   */
  private $spellcheckCollation;

  /**
   * Construct object
   */
  public function __construct(Response $response) {
    $this->response = $response;
  }

  /**
   * Get all documents
   *
   * @return array
   */
  public function getDocuments() {
    $this->checkParsed();
    return $this->documents;
  }

  /**
   * Get facet field
   *
   * @param string $fieldName
   * @return array
   */
  public function getFacetField($fieldName) {
    $this->checkParsed();
    return ( isset($this->facetFields[$fieldName]) ) ? $this->facetFields[$fieldName] : null;
  }

  /**
   * Return facet fields
   * @return array
   */
  public function getFacetFields() {
    $this->checkParsed();
    return $this->facetFields;
  }

  /**
   * Gets number of documents found
   *
   * @return int
   */
  public function getNumFound() {
    $this->checkParsed();
    return $this->numFound;
  }

  /**
   * Return spellcheck collation field
   *
   * @return string
   */
  public function getSpellCheckCollation() {
    $this->checkParsed();
    return $this->spellcheckCollation;
  }

  /**
   * Return spellcheck fields
   *
   * @return array
   */
  public function getSpellCheck() {
    $this->checkParsed();
    return $this->spellcheck;
  }

  /**
   * Check if json is parsed, if not parse it
   */
  protected function checkParsed() {
    if (!$this->isParsed)
      $this->parse();
  }

  /**
   * Parse data
   */
  protected function parse() {
    $data = unserialize( $this->response->getContent() );
    $this->facetFields = array();

    // set data
    $this->documents = $data['response']['docs'];
    $this->numFound = $data['response']['numFound'];
    if (isset($data['facet_counts']['facet_fields']))
      $this->facetFields = $data['facet_counts']['facet_fields'];

    if (isset($data['facet_counts']['facet_queries']))
      $this->facetFields = array_merge($this->facetFields, $data['facet_counts']['facet_queries']);

    if (isset($data['spellcheck']['suggestions'])) {
      $this->spellcheck = $data['spellcheck']['suggestions'];

      if ( isset ( $this->spellcheck['collation'] ) ) {
          $this->spellcheckCollation = $this->spellcheck['collation'];
          unset($this->spellcheck['collation']);
      }
    }

    $this->isParsed = true;
  }

}