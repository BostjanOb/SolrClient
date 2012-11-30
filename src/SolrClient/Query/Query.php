<?php

namespace SolrClient\Query;

/**
 * Solr Query Builder
 *
 * @license MIT
 * @author  Bostjan Oblak <bostjan@muha.cc>
 */
class Query implements \Serializable
{

  /**
   * Query parameters
   *
   * @var array
   */
  private $params = array();

  /**
   * Constrct object
   *
   * @param string $query
   */
  public function __construct($query = '*:*')
  {
    // set initial values
    $this->params['facet.field'] = array();
    $this->params['wt'] = 'phps';
    $this->params['version'] = '2.2';
    $this->params['qt'] = 'search';

    $this->setStart(0);

    $this->setQuery($query);
  }

  /**
   *
   * @return array
   */
  public function getPostQueryParams()
  {
    return $this->params;
  }

  /**
   * Construct search query
   *
   * @return string
   */
  public function getConstructedUrl()
  {
    $queryString = http_build_query($this->params);
    $queryString = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $queryString);

    return $queryString;
  }

  /**
   * Add ne field query
   *
   * @param string $field
   * @param string $value
   * @param string $tag
   * @return Query
   */
  public function addFieldQuery($field, $value, $tag = null)
  {
    $tagPrefix = '';
    $fq = "{$field}:{$value}";

    if (null != $tag)
      $tagPrefix = '{!tag=' . $tag . '}';

    if (is_array($value)) {
      array_walk($value, function(&$item, $key, $field) {
            $item = "{$field}:{$item}";
          }, $field);
      $fq = implode(' OR ', $value);
    }

    $this->params['fq'][] = $tagPrefix . $fq;
    return $this;
  }

  /**
   * Add field query as text (for raw field queries)
   *
   * @param string $text
   * @return Query
   */
  public function addFieldQueryText($text)
  {
    $this->params['fq'][] = "{$text}";
    return $this;
  }

  /**
   * Add new field range query
   *
   * @param string $field
   * @param string $from
   * @param string $to
   * @return Query
   */
  public function addFieldRange($field, $from = '*', $to = '*')
  {
    if (empty($from))
      $from = '*';

    if (empty($to))
      $to = '*';

    $this->params['fq'][] = "{$field}:[{$from} TO {$to}]";
    return $this;
  }

  /**
   * Sort by
   *
   * @param type $text
   * @return Query
   */
  public function addSort($field)
  {
    $this->params['sort'][] = $field;
    return $this;
  }

  /**
   * Enable or disable facet
   *
   * @param bool $flag
   * @return Query
   */
  public function setFacet($flag)
  {
    $this->params['facet'] = ($flag) ? 'on' : 'offF';
    return $this;
  }

  /**
   * Get facet status
   *
   * @return bool
   */
  public function getFacet()
  {
    $v = $this->getValue('facet');
    return ( $v != null && $v == 'on' );
  }

  /**
   * Sets indent
   *
   * @param bool $flag
   * @return Query
   */
  public function setIndent($flag)
  {
    $this->params['indent'] = ($flag) ? 'on' : 'off';
    return $this;
  }

  /**
   * Gets indent
   *
   * @return bool
   */
  public function getIndent()
  {
    $v = $this->getValue('indent');
    return ( $v != null && $v == 'on' );
  }

  /**
   * Add facet field
   *
   * @param string $fieldName
   * @return Query
   */
  public function addFacetField($fieldName)
  {
    $this->params['facet.field'][] = $fieldName;
    return $this;
  }

  /**
   * Add facet query
   *
   * @param string $query
   * @return Query
   */
  public function addFacetQuery($query)
  {
    $this->params['facet.query'][] = $query;
    return $this;
  }

  /**
   * Remove all facet fields
   *
   * @return Query
   */
  public function removeAllFacetFields()
  {
    $this->params['facet.field'] = array();
    return $this;
  }

  /**
   * Gets facet fields
   *
   * @return array
   */
  public function getFacetFields()
  {
    return $this->getValue('facet.field');
  }

  /**
   * Removes field from facet fields
   *
   * @param string $fieldName
   * @return Query
   */
  public function removeFacetField($fieldName)
  {
    if (false !== ($key = array_search($fieldName, $this->params['facet.field']) ))
      unset($this->params['facet.field'][$key]);

    return $this;
  }

  /**
   * Is field facet
   *
   * @param string $fieldName
   * @return bool
   */
  public function isFieldFacet($fieldName)
  {
    return \in_array($fieldName, $this->params['facet.field']);
  }

  /**
   * Sets the maximum number of constraint counts that should be returned for the facet fields
   *
   * @param int $limit
   * @return Query
   */
  public function setFacetLimit($limit, $fieldName = null)
  {
    $this->setFacetFieldParamValue($fieldName, 'limit', $limit);
    return $this;
  }

  /**
   * Returns the maximum number of constraint counts that should be returned for the facet fields
   *
   * @return int
   */
  public function getFacetLimit($fieldName = null)
  {
    return $this->getFacetValue($fieldName, 'limit');
  }

  /**
   * Sets the minimum counts for facet fields should be included in the response.
   *
   * @param int $count
   * @return Query
   */
  public function setFacetMinCount($count, $fieldName = null)
  {
    $this->setFacetFieldParamValue($fieldName, 'mincount', $count);
    return $this;
  }

  /**
   * Sets the minimum counts for facet fields should be included in the response.
   *
   * @param int $count
   * @return Query
   */
  public function setFacetPrefix($prefix, $fieldName = null)
  {
    $this->setFacetFieldParamValue($fieldName, 'prefix', $prefix);
    return $this;
  }

  /**
   * Gets the minimum counts for facet fields should be included in the response.
   *
   * @return int
   */
  public function getFacetMinCount($fieldName = null)
  {
    return $this->getFacetValue($fieldName, 'mincount');
  }

  /**
   * Sets the maximum number of documents
   *
   * @param int $rows
   * @return Query
   */
  public function setRows($rows)
  {
    $this->params['rows'] = $rows;
    return $this;
  }

  /**
   * Returns the maximum number of documents
   *
   * @return int
   */
  public function getRows()
  {
    return $this->getValue('rows');
  }

  /**
   * Sets start
   *
   * @param int $start
   * @return Query
   */
  public function setStart($start)
  {
    $this->params['start'] = $start;
    return $this;
  }

  /**
   * Gets start
   *
   * @return int
   */
  public function getStart()
  {
    return $this->params['start'];
  }

  /**
   * Sets the main query
   *
   * @param string $q
   * @return Query
   */
  public function setQuery($q)
  {
    $this->params['q'] = $q;
    return $this;
  }

  /**
   * Returns the main query
   *
   * @return string
   */
  public function getQuery()
  {
    return $this->getValue('q');
  }

  /**
   * Enable or disable spellcheck
   *
   * @param bool $val
   * @return Query
   */
  public function enableSpellcheck($val = true)
  {
    $this->params['spellcheck'] = ($val) ? 'on' : 'off';
    $this->params['spellcheck.collate'] = ($val) ? 'on' : 'off';
    return $this;
  }

  /**
   * Build spellcheck
   *
   * @return Query
   */
  public function buildSpellcheck()
  {
      $this->params['spellcheck.build'] = 'true';
      return $this;
  }

  /**
   * Gets main facet option
   *
   * @param string $field
   * @return string|null
   */
  private function getFacetValue($field, $param)
  {
    $f = 'facet.' . $param;

    if (null != $field)
      $f = 'f.' . $field . '.facet.' . $param;

    return ( isset($this->params[$f]) ) ? $this->params[$f] : null;
  }

  /**
   * Set facet param value
   *
   * @param string $field
   * @param string $param
   * @param string $value
   */
  private function setFacetFieldParamValue($field, $param, $value)
  {
    $f = 'facet.' . $param;

    if (null != $field)
      $f = 'f.' . $field . '.facet.' . $param;

    $this->params[$f] = $value;
  }

  private function getValue($name)
  {
    return ( isset($this->params[$name]) ) ? $this->params[$name] : null;
  }

  /**
   * Serilaize object
   *
   * @return string
   */
  public function serialize()
  {
    return serialize($this->params);
  }

  /**
   * Unserialize object
   *
   * @param string $data
   */
  public function unserialize($data)
  {
    $this->params = unserialize($data);
  }

  /**
   * @param $qt
   */
  public function setQueryType($qt)
  {
    $this->params['qt'] = $qt;
  }

}
