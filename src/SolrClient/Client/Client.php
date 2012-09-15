<?php

namespace SolrClient\Client;

use Zend\Uri\Http as HttpUri,
    Zend\Http,
    SolrClient\Document\Document,
    SolrClient\Query\Result,
    SolrClient\Query\Query;

/**
 * Solr Client class
 *
 * @license MIT
 * @author  Bostjan Oblak <bostjan@muha.cc>
 */
class Client {

    /**
     * @var HttpUri $_host
     */
    private $uri;

    /**
     * @var string
     */
    private $selectPath;

    /**
     * @var string
     */
    private $updatePath;
    
    /**
     * @var SolrClient\Logging\LoggerInterface
     */
    private $logger;

    //private static $_logger;

    public function __construct(HttpUri $uri) {
        $this->uri = $uri;
    }
    
    /**
     * Sets logging
     * 
     * @param \SolrClient\Logging\LoggerInterface $logger
     * @return \SolrClient\Client\Client
     */
    public function setLogger(\SolrClient\Logging\LoggerInterface $logger) {
        $this->logger = $logger;
        return $this;
    }

    /**
     * @return HttpUri
     */
    public function getUri() {
        return $this->uri;
    }

    /**
     * Add new document to solr
     *
     * @param Document $document
     * @param bool $commitWithin
     * @return type
     */
    public function addDocument(Document $document, $commitWithin = false) {
        $doc = array($document);

        return $this->addDocuments($doc, $commitWithin);
    }

    /**
     * Add new documents to solr
     *
     * @param array $documents
     * @param bool $commitWithin
     */
    public function addDocuments(array $documents, $commitWithin = false) {
        $rawPost = '<add>';
        foreach ($documents as $doc)
            $rawPost .= $doc->getXml();

        $rawPost .= '</add>';

        $this->rawPost($rawPost);

        if ($commitWithin)
            $this->commit();
    }

    /**
     * Delete document by id
     *
     * @param array|string $ids
     * @param true $commit
     */
    public function deleteById($ids, $commit = false) {
        if (!is_array($ids))
            $ids = array($ids);


        array_walk($ids, function(&$item) {
                    $item = '<id>' . $item . '</id>';
                });

        $rawPost = '<delete>' . implode('', $ids) . '</delete>';

        $this->rawPost($rawPost);

        if ($commit)
            $this->commit();
    }

    /**
     * Commit changes
     *
     * @param bool $waitFlush Block until index changes are flushed to disk
     * @param bool $waitSearcher Block until a new searcher is opened and registered as the main query searcher, making the changes visible
     */
    public function commit($optimize = false, $waitFlush = true, $waitSearcher = true) {

        $wf = $waitFlush ? 'true' : 'false';
        $ws = $waitSearcher ? 'true' : 'false';
        $op = ($optimize) ? 'true' : 'false';
        $rawPost = '<commit waitFlush="' . $wf . '" waitSearcher="' . $ws . '" optimize="' . $op . '" />';

        $this->rawPost($rawPost);
    }

    /**
     * Optimize solr
     *
     * @param bool $waitFlush
     * @param bool $waitSearcher
     * @param int $maxSegments
     */
    public function optimize($waitFlush = true, $waitSearcher = true, $maxSegments = 1) {
        $wf = $waitFlush ? 'true' : 'false';
        $ws = $waitSearcher ? 'true' : 'false';

        $rawPost = '<optimize waitFlush="' . $wf . '" waitSearcher="' . $ws . '" maxSegments="' . $maxSegments . '" />';

        $this->rawPost($rawPost);
    }

    /**
     * Make query to SOLR
     *
     * @param Query $query
     * @return QueryResult
     */
    public function query(Query $query) {
        $selectUri = clone $this->uri;

        //$uri->setPath( $uri->getPath() . '/' . $this->getSelectPath() );
        $selectUri->setPath($selectUri->getPath() . $this->selectPath);

        $selectUri->setQuery($query->getConstructedUrl());

        $request = new Http\Request();
        $request->setUri($selectUri)
                ->setMethod(Http\Request::METHOD_GET);

        $client = new Http\Client($request->getUri());
        $client->setAdapter('Zend\Http\Client\Adapter\Curl');

         if ($this->logger) $this->logger->start($selectUri);

        $response = $client->send($request);

        if ($this->logger) $this->logger->end();

        if ($response->getStatusCode() != 200)
            throw new \Zend\Http\Exception\RuntimeException('Query error. Reason: ' . $response->getReasonPhrase());

        return new Result($response);
    }

    /**
     * Set select path
     *
     * @param string $path
     * @return Client
     */
    public function setSelectPath($selectPath) {
        $this->selectPath = $selectPath;
        return $this;
    }

    /**
     * @return string
     */
    public function getSelectPath() {
        return $this->selectPath;
    }

    /**
     * Set update path
     *
     * @param string $path
     * @return Client
     */
    public function setUpdatePath($updatePath) {
        $this->updatePath = $updatePath;
        return $this;
    }

    /**
     * @return string
     */
    public function getUpdatePath() {
        return $this->updatePath;
    }

    /**
     * Make raw post request
     *
     * @param string $body
     */
    protected function rawPost($body) {
        $updateUri = clone $this->uri;
        $updateUri->setPath($updateUri->getPath() . $this->updatePath);

        $request = new Http\Request();
        $request->setUri($updateUri)
                ->setMethod(Http\Request::METHOD_POST)
                ->setContent($body);

        $client = new Http\Client($request->getUri());
        $client->setAdapter('Zend\Http\Client\Adapter\Curl');

        if ($this->logger) $this->logger->start($updateUri);

        $response = $client->send($request);

        if ($this->logger) $this->logger->end();

        if ($response->getStatusCode() != 200)
            throw new \Zend\Http\Exception\RuntimeException('Query error. Reason: ' . $response->getReasonPhrase());
    }

}