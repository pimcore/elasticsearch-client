# Configuration

Following you see all the available configuration options for a custom es client.

```yaml
pimcore_elasticsearch_client:
    es_clients:
        sample:
            hosts: ['elastic:9200']
            username: 'elastic'
            password: 'somethingsecret'
            logger_channel: 'pimcore.elasicsearch'
            
            #optional options
            ca_bundle: 'path/to/ca/cert'
            ssl_key: 'path/to/ssl/key'
            ssl_cert: 'path/to/ssl/cert'
            ssl_password: 'secretePW'
            ssl_verification: false #false is the default value
            http_options:
                proxy: 'http://localhost:8125'
            cloud_id: '123456789'
            api_key: 'secret-apikey'
```


### Elasticsearch Cloud

Following you see a example on how to connect to Elasticsearch Cloud. 

For further details checkout the [Elasticsearch Docs](https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/connecting.html)

```yaml
pimcore_elasticsearch_client:
    es_clients:
        cloud:
            cloud_id: '123456789'
            api_key: 'secret-apikey'
```