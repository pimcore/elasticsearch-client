# Pimcore Elasticsearch Client

This bundle provides a central configuration and factory feature for creating elasticsearch clients to be used in 
other bundles. 

It allows to configure one or more elasticsearch clients with different configuration settings. The corresponding 
settings are then registered as services and can be injected into any services. 

Supported elasticsearch version: Elasticsearch 8

## Installation

This bundle is a standard symfony bundle. If not required and activated by another bundle, it can be enabled by 
adding it to the `bundles.php` of your application. 


## Configuration

The Configuration takes place in symfony configuration tree where multiple elasticsearch clients can be configured as follows. 
It is possible to configure one or more clients if necessary. 
By default, a `default` client with host set to `localhost:9200` is available and can be customized. 


For details on the configuration opens have a look at inline documentation via command 
`bin/console config:dump-reference PimcoreElasticsearchClientBundle`


```yaml
pimcore_elasticsearch_client:
    es_clients:
        default:
            hosts: ['elastic:9200']
            username: 'elastic'
            password: 'somethingsecret'
            logger_channel: 'pimcore.elasicsearch'
        statistics:
            hosts: ['statistics-node:9200']
            logger_channel: 'pimcore.statistics'
 
```

## Integration into other Bundles

For each of the configured clients, a client service is registered in the symfony container. The naming schema follows 
`pimcore.elasticsearch_client.<CLIENT_CONFIGURATION_NAME>`. These client services can be injected into and used by other
services then. 

