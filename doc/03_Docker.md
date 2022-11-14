# Docker

We created a sample docker setup for running Elasticsearch locally on your machine.

Feel free to customize and use this as a template to create your own setup.

### Docker Compose Setup

This will create an Elasticsearch container (`elastic`) and a Kibana container (`kibana`).
They will communicate between each other via an encrypted connection! If you do not need an 
encrypted connection just comment out the ssl stuff (see code comments)

```yaml
#docker-compose.yaml

services:
    php:
        user: '1000:1000' # set to your uid:gid

    supervisord:
        user: '1000:1000' # set to your uid:gid
        
    db:
        ports:
            - "3306:3306"

    create_certs:
        container_name: elastic_create_certs
        image: elasticsearch:8.4.3
        command: >
            bash -c '
                if [[ ! -f ./certs/certs-bundle.zip ]]; then
                    echo "Generating CA";
                    #bin/elasticsearch-certutil ca --silent --pass "password" --pem --out ./certs/certs-bundle.zip;
                    bin/elasticsearch-certutil ca --silent --pem --out ./certs/certs-bundle.zip;
                    unzip ./certs/certs-bundle.zip -d ./certs;
                fi
            
                if [ ! -f ./certs/certs.zip ]; then
                    echo "Creating certs";
                    echo -ne \
                      "instances:\n"\
                      "  - name: elastic\n"\
                      "    dns:\n"\
                      "      - elastic\n"\
                      "      - localhost\n"\
                      "    ip:\n"\
                      "      - 127.0.0.1\n"\
                      > ./certs/instances.yml;
                    bin/elasticsearch-certutil cert --silent --pem --ca-cert ./certs/ca/ca.crt --ca-key ./certs/ca/ca.key --in ./certs/instances.yml --out ./certs/certs.zip;
                    unzip ./certs/certs.zip -d ./certs;
                fi
            
                echo "Setting file permissions"
                chown -R root:root ./certs;
                find . -type d -exec chmod 750 \{\} \;;
                find . -type f -exec chmod 640 \{\} \;;
            
                echo "Waiting for Elasticsearch availability";
                until curl --insecure -s --cacert ./certs/ca/ca.crt https://elastic:9200 | grep -q "missing authentication credentials"; do sleep 30; done;
              
                echo "Setting kibana_system password";
                until curl --insecure -s -X POST --cacert ./certs/ca/ca.crt -u "elastic:somethingsecret" -H "Content-Type: application/json" https://elastic:9200/_security/user/kibana_system/_password -d "{\"password\":\"somethingsecret\"}" | grep -q "^{}"; do sleep 10; done;
            
                echo "All done!"
            '
        user: "0"
        working_dir: /usr/share/elasticsearch
        volumes:
            - pimcore-enterprise-elastic-certs:/usr/share/elasticsearch/certs

    elastic:
        container_name: elastic-10
        image: elasticsearch:8.4.3
        environment:
            - discovery.type=single-node
            - "ES_JAVA_OPTS=-Xms1g -Xmx1g"
            - xpack.security.enabled=true
            - ELASTIC_PASSWORD=somethingsecret

            # DISABLE LINES BELOW IF SSL IS NOT NEEDED!

            #CONFIG BELOW USES PEM
            - xpack.security.http.ssl.enabled=true
            - xpack.security.http.ssl.key=certs/elastic/elastic.key
            - xpack.security.http.ssl.certificate=certs/elastic/elastic.crt
            - xpack.security.http.ssl.certificate_authorities=certs/ca/ca.crt
            - xpack.security.http.ssl.verification_mode=certificate

            - xpack.security.transport.ssl.enabled=true
            - xpack.security.transport.ssl.key=certs/elastic/elastic.key
            - xpack.security.transport.ssl.certificate=certs/elastic/elastic.crt
            - xpack.security.transport.ssl.certificate_authorities=certs/ca/ca.crt
            - xpack.security.transport.ssl.verification_mode=certificate

            # CONFIG BELOW USES KEYSTORES
            #- xpack.security.transport.ssl.enabled=true
            #- xpack.security.transport.ssl.keystore.password=elastic
            #- xpack.security.transport.ssl.truststore.password=elastic
            #- xpack.security.transport.ssl.keystore.path=certs/elasticsearch01.p12
            #- xpack.security.transport.ssl.verification_mode=certificate

            #- xpack.security.http.ssl.enabled=true
            #- xpack.security.http.ssl.keystore.password=elastic
            #- xpack.security.http.ssl.truststore.password=elastic
            #- xpack.security.http.ssl.keystore.path=certs/elasticsearch01.p12
            #- xpack.security.http.ssl.verification_mode=certificate
        ports:
            - 9200:9200
        volumes:
            - pimcore-enterprise-elastic:/usr/share/elasticsearch/data
            - pimcore-enterprise-elastic-certs:/usr/share/elasticsearch/config/certs
        deploy:
            resources:
                limits:
                    cpus: '1'
                    memory: '2G'

    kibana:
        container_name: kibana-10
        image: kibana:8.4.3
        ports:
            - 5601:5601
        environment:
            - ELASTICSEARCH_HOSTS=https://elastic:9200
            - XPACK_SECURITY_ENABLED=true

            - ELASTICSEARCH_USERNAME=kibana_system
            - ELASTICSEARCH_PASSWORD=somethingsecret

            # DISABLE LINES BELOW IF SSL IS NOT NEEDED!
            - ELASTICSEARCH_SSL_CERTIFICATEAUTHORITIES=config/certs/ca/ca.crt

            #- ELASTICSEARCH_SSL_CERTIFICATEAUTHORITIES=config/certs/ca/ca.crt
            #- ELASTICSEARCH_SSL_VERIFICATIONMODE=certificate
            #- SERVER_SSL_ENABLED=true
            #- SERVER_SSL_KEY=config/certs/kibana.key
            #- SERVER_SSL_CERTIFICATE=config/certs/kibana.crt
            #- SERVER_SSL_PASSWORD=kibana
            #- SERVER_SSL_KEYPASSPHRASE=kibana
        volumes:
            - pimcore-enterprise-elastic-certs:/usr/share/kibana/config/certs

volumes:
    pimcore-enterprise-elastic:
    pimcore-enterprise-elastic-certs:
```

Use `docker-compose up -d` to run the configured stack.

Also `docker compose logs -f` could be helpful to debug certain errors.