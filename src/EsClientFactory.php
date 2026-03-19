<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\ElasticsearchClientBundle;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Psr\Log\LoggerInterface;

class EsClientFactory
{
    public static function create(LoggerInterface $logger, array $configuration): Client
    {
        if (isset($configuration['dsn']) && $configuration['dsn'] !== null && $configuration['dsn'] !== '') {
            $configuration = self::parseDsn($configuration['dsn'], $configuration);
        }

        $builder = ClientBuilder::create()
            ->setHosts($configuration['hosts'])
            ->setLogger($logger);

        if (isset($configuration['username'], $configuration['password'])) {
            $builder->setBasicAuthentication($configuration['username'], $configuration['password']);
        }

        if (isset($configuration['cloud_id'], $configuration['api_key'])) {
            $builder
                ->setElasticCloudId($configuration['cloud_id'])
                ->setApiKey($configuration['api_key']);
        }

        if (isset($configuration['ca_bundle'])) {
            $builder->setCABundle($configuration['ca_bundle']);
        }

        if (isset($configuration['ssl_key']) && $configuration['ssl_cert']) {
            $builder
                ->setSSLKey($configuration['ssl_key'], $configuration['ssl_password'] ?? null)
                ->setSSLCert($configuration['ssl_cert'], $configuration['ssl_password'] ?? null);
        }

        if (isset($configuration['ssl_verification'])) {
            $builder->setSSLVerification($configuration['ssl_verification']);
        }

        if (isset($configuration['http_options'])) {
            $builder->setHttpClientOptions($configuration['http_options']);
        }

        return $builder->build();
    }

    /**
     * Parse DSN into config array, merging with existing config.
     * DSN format: elasticsearch://user:pass@host:port?ssl=bool&ssl_verify=bool
     *
     * DSN values override file-based config for: hosts, username, password, ssl_verification.
     * The `ssl` query parameter controls whether HTTPS or HTTP is used (default: false).
     * The `ssl_verify` query parameter controls certificate verification (independent of `ssl`).
     * Other config keys (logger_channel, ca_bundle, ssl_key, ssl_cert, cloud_id, api_key) remain from file config.
     */
    private static function parseDsn(string $dsn, array $configuration): array
    {
        $parsed = parse_url($dsn);
        if ($parsed === false) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid Elasticsearch DSN: "%s"',
                $dsn,
            ));
        }

        $host = $parsed['host'] ?? 'localhost';
        $port = $parsed['port'] ?? 9200;

        $queryParams = [];
        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $queryParams);
        }

        $useSsl = ($queryParams['ssl'] ?? 'false') === 'true';
        $protocol = $useSsl ? 'https' : 'http';
        $configuration['hosts'] = [sprintf('%s://%s:%d', $protocol, $host, $port)];

        if (isset($parsed['user'])) {
            $configuration['username'] = rawurldecode($parsed['user']);
        }
        if (isset($parsed['pass'])) {
            $configuration['password'] = rawurldecode($parsed['pass']);
        }

        if (isset($queryParams['ssl_verify'])) {
            $configuration['ssl_verification'] = $queryParams['ssl_verify'] === 'true';
        }

        return $configuration;
    }
}
