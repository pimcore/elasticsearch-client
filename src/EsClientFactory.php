<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\ElasticsearchClientBundle;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Psr\Log\LoggerInterface;

class EsClientFactory
{
    public static function create(LoggerInterface $logger, array $configuration): Client
    {
        $builder = ClientBuilder::create()
            ->setHosts($configuration['hosts'])
            ->setLogger($logger);

        if (isset($configuration['username'], $configuration['password'])) {
            $builder->setBasicAuthentication($configuration['username'], $configuration['password']);
        }

        if(isset($configuration['cloud_id'], $configuration['api_key'])){
            $builder
                ->setElasticCloudId($configuration['cloud_id'])
                ->setApiKey($configuration['api_key']);
        }

        if(isset($configuration['ca_bundle'])){
            $builder->setCABundle($configuration['ca_bundle']);
        }

        if(isset($configuration['ssl_key']) && $configuration['ssl_cert']){
            $builder
                ->setSSLKey($configuration['ssl_key'], $configuration['ssl_password'] ?? null)
                ->setSSLCert($configuration['ssl_cert'], $configuration['ssl_password'] ?? null);

            if(isset($configuration['ssl_verification'])){
                $builder->setSSLVerification($configuration['ssl_verification']);
            }
        }

        if(isset($configuration['http_options'])){
            $builder->setHttpClientOptions($configuration['http_options']);
        }

        return $builder->build();
    }
}
