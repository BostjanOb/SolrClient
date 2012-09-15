<?php

namespace SolrClient\Document;

/**
 * Field in document
 *
 * @license MIT
 * @author  Bostjan Oblak <bostjan@muha.cc>
 */
class Field
{
  /**
   * Field name
   *
   * @var string $name
   */
  private $name;

  /**
   * Field value
   *
   * @var array $values
   */
  private $values = array();

  /**
   * Field boost
   *
   * @var float $boost
   */
  private $boost;

  /**
   * Construct document field
   *
   * @param string $name
   * @param string|array $value
   * @param float $boost
   */
  function __construct($name, $values, $boost = 0) {
    $this->name = $name;

    if ( !is_array($values) )
      $values = array($values);

    $this->values = $values;
    $this->boost = $boost;
  }

  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @param string $name
   * @return Field
   */
  public function setName($name) {
    $this->name = $name;
    return $this;
  }

  /**
   * Add value to field
   *
   * @param string $value
   * @return Field
   */
  public function addValue($value) {
    $this->values[] = $value;
    return $this;
  }

  /**
   * Add values to field
   *
   * @param array $values
   * @return Field
   */
  public function addValues(array $values) {
    $this->values = array_merge($this->values, $values);
    return $this;
  }

  /**
   * @return array
   */
  public function getValues() {
    return $this->values;
  }

  /**
   * @param string|array $value
   * @return Field
   */
  public function setValues($values) {
    if ( !is_array($values) )
      $values = array($values);

    $this->values = $values;
    return $this;
  }

  /**
   * @return float
   */
  public function getBoost() {
    return $this->boost;
  }

  /**
   * @param float $boost
   * @return Field
   */
  public function setBoost($boost) {
    $this->boost = $boost;
    return $this;
  }

}