<?php

return array(
    'solr' => array(
        'connection' => array(
            'solr_default' => array(
                'host' => 'localhost',
                'port' => '8983',
                'user' => '',
                'password' => '',
                'path' => '/solr',
                'scheme' => 'http'
            ),
        ),
        'configuration' => array(
            'solr_default' => array(
                'select_path' => '/select',
                'update_path' => '/update',
                'resultClass' => '\SolrClient\Query\Result'
            ),
        ),
        'logger_collector' => array(
            'solr_default' => 'solr.solr_default'
        )
    ),
    
    'view_manager' => array(
        'template_map' => array(
            'zend-developer-tools/toolbar/solrClient' => __DIR__ . '/../view/zend-developer-tools/toolbar/solrClient.phtml',
        ),
    ),
    'zenddevelopertools' => array(
        'profiler' => array(
            'collectors' => array(
                'solr_default' => 'solr.logger_collector.default',
            ),
        ),
        'toolbar' => array(
            'entries' => array(
                'solr_default' => 'zend-developer-tools/toolbar/solrClient',
            ),
        ),
    ),
);