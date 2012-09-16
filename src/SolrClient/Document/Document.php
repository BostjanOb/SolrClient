<?php

namespace SolrClient\Document;

/**
 * Document for inserting to Solr
 *
 * @license MIT
 * @author  Bostjan Oblak <bostjan@muha.cc>
 */
class Document implements \ArrayAccess, \Countable {

    /**
     * All fields with their values
     * Keys are names, values are values
     *
     * @var array $fields
     */
    protected $fields = array();

    /**
     * Document boost
     *
     * @var float $boost
     */
    protected $boost = 0;

    /**
     * For iterator
     *
     * @var int $position
     */
    private $position = 0;

    /**
     * Construct object
     */
    public function __construct() {
        $this->position = 0;
    }

    /**
     * Add field to document
     *
     * @param string $field
     * @param string $value
     * @param float $boostValue
     * @return Document
     */
    public function addField($field, $value = null, $boostValue = 0) {

        if (!\is_a($field, 'Field')) {
            if (empty($field))
                throw new \Exception('Field could not be empty');

            $doc = new Field($field, $value, $boostValue);
        }
        else
            $doc = $field;

        if ($this->fieldExists($doc->getName()))
            $this->fields[$doc->getName()]->addValues($doc->getValues());
        else
            $this->fields[$doc->getName()] = $doc;

        return $this;
    }

    /**
     * Set boost value for document
     *
     * @param float $docBoostValue
     * @return Document
     */
    public function setBoost($docBoostValue) {
        $this->boost = $docBoostValue;
        return $this;
    }

    /**
     * Clear document
     */
    public function clear() {
        $this->fields = array();
    }

    /**
     * Delete field from document
     * If filed is multivalued, it removes all values
     *
     * @param string $name
     */
    public function deleteField($name) {
        unset($this->fields[$name]);
    }

    /**
     * Checks if field exists in document
     *
     * @param string $name
     * @return bool
     */
    public function fieldExists($name) {
        return isset($this->fields[$name]);
    }

    /**
     * Get field values. If field does not exists, return null
     *
     * @param string $name
     * @return string|null
     */
    public function getField($name) {
        if ($this->fieldExists($name))
            return $this->fields[$name];

        return null;
    }

    /**
     * Get number of fileds on document
     *
     * @return int
     */
    public function getFieldCount() {
        return count($this->fields);
    }

    /**
     * Get names of fileds on document
     *
     * @return array
     */
    public function getFieldNames() {
        return \array_keys($this->fields);
    }

    /**
     * Document as array
     *
     * @return array
     */
    public function toArray() {
        return $this->fields;
    }

    /**
     * Construct XML for inserting into SOLR
     * @return string
     */
    public function getXml() {
        $r = '<doc>';
        foreach ($this->fields as $v) {
            foreach ($v->getValues() as $value) {
                $r .= '<field name="' . $v->getName() . '">' . $this->prepareValue($value) . '</field>';
            }
        }
        $r .= '</doc>';

        return $r;
    }

    // ArrayAccess interface

    /**
     * @param int|string $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return isset($this->fields[$offset]);
    }

    /**
     * @param int|string $offset
     * @return string|null
     */
    public function offsetGet($offset) {
        return isset($this->fields[$offset]) ? $this->fields[$offset] : null;
    }

    /**
     * @param int|string $offset
     * @param string $value
     */
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->fields[] = $value;
        } else {
            $this->fields[$offset] = $value;
        }
    }

    /**
     * @param int|string $offset
     */
    public function offsetUnset($offset) {
        unset($this->fields[$offset]);
    }

    /**
     * @param string $value
     * @return string
     */
    private function prepareValue($value) {
        $value = \htmlspecialchars($value, \ENT_NOQUOTES);
        return $value;
    }

    /**
     * Count added fields
     * 
     * @return integer
     */
    public function count() {
        return $this->getFieldCount();
    }

}