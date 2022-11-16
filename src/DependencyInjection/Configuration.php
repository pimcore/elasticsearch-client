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

namespace Pimcore\Bundle\ElasticsearchClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('pimcore_elasticsearch_client');

        $rootNode = $treeBuilder->getRootNode();

        /** @phpstan-ignore-next-line */
        $rootNode->children()
            ->arrayNode('es_clients')
                ->useAttributeAsKey('name')
                    ->prototype('scalar')
                ->end()
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('name')->end()
                        ->arrayNode('hosts')
                            ->prototype('scalar')->end()
                            ->defaultValue(['localhost:9200'])
                            ->info('List of elasticsearch hosts, including their ports')
                        ->end()
                        ->scalarNode('logger_channel')
                            ->defaultValue('pimcore.elasticsearch.default')
                            ->info('Logger channel to be used for elasticsearch client logs')
                        ->end()
                        ->scalarNode('username')
                            ->info('Username for elasticsearch authentication')
                        ->end()
                        ->scalarNode('password')
                            ->info('Password for elasticsearch authentication')
                        ->end()
                        ->scalarNode('ca_bundle')
                            ->info('Path to certificate authority file (.crt)')
                        ->end()
                        ->scalarNode('ssl_key')
                            ->info('Path to private SSL key file (.key)')
                        ->end()
                        ->scalarNode('ssl_cert')
                            ->info('Path to PEM formatted SSL cert file (.cert)')
                        ->end()
                        ->scalarNode('ssl_password')
                            ->info('If private key and certificate require a password (default: null)')
                        ->end()
                        ->booleanNode('ssl_verification')
                            ->info('Enable or disable the SSL verification (default: false)')
                        ->end()
                        ->arrayNode('http_options')
                            ->prototype('scalar')->end()
                            ->info('List of http client options')
                        ->end()
                        ->scalarNode('cloud_id')
                            ->info('Elastic Cloud Id for elasticsearch cloud authentication')
                        ->end()
                        ->scalarNode('api_key')
                            ->info('Elastic Cloud API key for elasticsearch cloud authentication')
                        ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
