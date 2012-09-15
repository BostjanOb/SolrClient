# Apache Solr Client for Zend Framework 2

Features :

  - Adding, updating and deleting documents
  - Query builder
  - Facets
  - Spellcheck

## Requirements
[Zend Framework 2](http://www.github.com/zendframework/zf2)
[Apache Solr] (http://lucene.apache.org/solr/)

## Installation

 1.  Add `"bostjanob/solr-client": "dev-master"` to your `composer.json`
 2.  Run `php composer.phar install`
 3.  Enable the module in your `config/application.config.php` by adding `SolrClient` to `modules`

#### Setting up your connection

Setup your connection by adding the module configuration to any valid ZF2 config file. This can be any file in autoload/
or a module configuration (such as the Application/config/module.config.php file).

```php
<?php
return array(
    'solr' => array(
        'connection' => array(
            'solr_default' => array(
                'host' => 'HOSTNAME',
                'port' => 'PORT',
                // for basic auth
                'user' => 'USERNAME', 
                'password' => 'PASSWORD',

                // web path where solr is installed
                'path' => '/', 

                // scheme: http or https
                'scheme' => 'http'
            ),
        ),
    ),
);
```

## Getting SolrClient

Access the solrClient using the following alias:

```php
<?php
$client = $this->getServiceLocator()->get('solr.solr_default');
```

## Inserting or updating documents
Coming soon...

## Deleting documents
Deleting documents by ID
```php
<?php
$solrClient->deleteById(1);
$solrClient->commit();

// more documents at once, and commit
$solrClient->deleteById(array(1,2,3,4), true);
```

## Commit, optimize
[More info about "commit" and "optimize"](http://wiki.apache.org/solr/UpdateXmlMessages#A.22commit.22_and_.22optimize.22)

#### Commit
```php
<?php
public function commit($optimize = false, $waitFlush = true, $waitSearcher = true)
```

#### Optimize
```php
<?php
    public function optimize($waitFlush = true, $waitSearcher = true, $maxSegments = 1)
```

## Query builder
Coming soon...