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

namespace Pimcore\Bundle\ElasticsearchClientBundle\DependencyInjection;

use Elastic\Elasticsearch\Client;
use Pimcore\Bundle\ElasticsearchClientBundle\EsClientFactory;
use Pimcore\Bundle\ElasticsearchClientBundle\SearchClient\SearchClient;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class PimcoreElasticsearchClientExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    const CLIENT_SERVICE_PREFIX = 'pimcore.elasticsearch_client.';

    const PIMCORE_CLIENT_PREFIX = 'pimcore.elasticsearch.custom_client.';

    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yaml');

        $definitions = [];

        foreach ($mergedConfig['es_clients'] as $name => $clientConfig) {
            if (isset($clientConfig['dsn']) && $clientConfig['dsn'] !== null && $clientConfig['dsn'] !== '') {
                $clientConfig = $this->parseDsn($clientConfig['dsn'], $clientConfig);
            }

            $definition = new Definition(Client::class);
            $definition->setFactory(EsClientFactory::class . '::create');
            $definition->setArgument('$logger', new Reference('logger'));
            $definition->setArgument('$configuration', $clientConfig);
            $definition->addTag('monolog.logger', ['channel' => $clientConfig['logger_channel']]);
            $definitions[self::CLIENT_SERVICE_PREFIX . $name] = $definition;

            $customClientDefinition = new Definition(SearchClient::class);
            $customClientDefinition->setArgument('$client', $definition);
            $definitions[self::PIMCORE_CLIENT_PREFIX . $name] = $customClientDefinition;
        }

        $container->addDefinitions($definitions);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('default_config.yaml');
    }

    /**
     * Parse DSN into config array, merging with existing config.
     * DSN format: elasticsearch://user:pass@host:port?ssl_verify=bool
     *
     * DSN values override file-based config for: hosts, username, password, ssl_verification.
     * Other config keys (logger_channel, ca_bundle, ssl_key, ssl_cert, cloud_id, api_key) remain from file config.
     */
    private function parseDsn(string $dsn, array $config): array
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
        $config['hosts'] = [sprintf('%s:%d', $host, $port)];

        if (isset($parsed['user'])) {
            $config['username'] = rawurldecode($parsed['user']);
        }
        if (isset($parsed['pass'])) {
            $config['password'] = rawurldecode($parsed['pass']);
        }

        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $queryParams);
            if (isset($queryParams['ssl_verify'])) {
                $config['ssl_verification'] = $queryParams['ssl_verify'] === 'true';
            }
        }

        return $config;
    }
}
