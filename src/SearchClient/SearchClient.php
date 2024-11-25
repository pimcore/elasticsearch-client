<?php
declare(strict_types=1);

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

namespace Pimcore\Bundle\ElasticsearchClientBundle\SearchClient;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Exception;
use Pimcore\SearchClient\Exception\ClientException;

/**
 * @internal
 */
final class SearchClient implements ElasticsearchClientInterface
{
    public function __construct(
        private readonly Client $client
    ) {
    }

    public function getOriginalClient(): Client
    {
        return $this->client;
    }

    /**
     * @throws ClientException
     */
    public function create(array $params): array
    {
        try {
            return $this->getArrayResponse($this->client->create($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Failed to create data');
        }
    }

    /**
     * @throws ClientException
     */
    public function search(array $params): array
    {
        try {
            return $this->getArrayResponse($this->client->search($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Failed to search data');
        }
    }

    /**
     * @throws ClientException
     */
    public function get(array $params): array
    {
        try {
            return $this->getArrayResponse($this->client->get($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Failed to get data');
        }
    }

    /**
     * @throws ClientException
     */
    public function exists(array $params): bool
    {
        try {
            return $this->getBoolResponse($this->client->exists($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithBool($exception, 'Failed to check if data exists');
        }
    }

    /**
     * @throws ClientException
     */
    public function count(array $params): array
    {
        try {
            return $this->getArrayResponse($this->client->count($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Failed to count data');
        }
    }

    /**
     * @throws ClientException
     */
    public function index(array $params): array
    {
        try {
            return $this->getArrayResponse($this->client->index($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Index operation failed');
        }
    }

    /**
     * @throws ClientException
     */
    public function bulk(array $params): array
    {
        try {
            return $this->getArrayResponse($this->client->bulk($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Bulk operation failed');
        }
    }

    /**
     * @throws ClientException
     */
    public function delete(array $params): array
    {
        try {
            return $this->getArrayResponse($this->client->delete($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Delete operation failed');
        }
    }

    /**
     * @throws ClientException
     */
    public function updateByQuery(array $params): array
    {
        try {
            return $this->getArrayResponse($this->client->updateByQuery($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Failed to update by query');
        }
    }

    /**
     * @throws ClientException
     */
    public function deleteByQuery(array $params): array
    {
        try {
            return $this->getArrayResponse($this->client->deleteByQuery($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Failed to delete by query');
        }
    }

    /**
     * @throws ClientException
     */
    public function createIndex(array $params): array
    {
        try {
            return $this->getArrayResponse($this->client->indices()->create($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Failed to create index');
        }
    }

    /**
     * @throws ClientException
     */
    public function openIndex(array $params): array
    {
        try {
            return $this->getArrayResponse($this->client->indices()->open($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Failed to open index');
        }
    }

    /**
     * @throws ClientException
     */
    public function closeIndex(array $params): array
    {
        try {
            return $this->getArrayResponse($this->client->indices()->close($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Failed to close index');
        }
    }

    /**
     * @throws ClientException
     */
    public function getAllIndices(array $params): array
    {
        try {
            $params = array_merge($params, ['format' => 'json']);

            return $this->getArrayResponse($this->client->cat()->indices($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Failed to get all indices');
        }
    }

    /**
     * @throws ClientException
     */
    public function existsIndex(array $params): bool
    {
        try {
            return $this->getBoolResponse($this->client->indices()->exists($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithBool($exception, 'Failed to check if index exists');
        }
    }

    /**
     * @throws ClientException
     */
    public function reIndex(array $params): array
    {
        try {
            return $this->getArrayResponse($this->client->reindex($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Failed to reindex');
        }
    }

    /**
     * @throws ClientException
     */
    public function refreshIndex(array $params = []): array
    {
        try {
            return $this->getArrayResponse($this->client->indices()->refresh($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Failed to refresh index');
        }
    }

    /**
     * @throws ClientException
     */
    public function flushIndex(array $params = []): array
    {
        try {
            return $this->getArrayResponse($this->client->indices()->flush($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Failed to flush index');
        }
    }

    /**
     * @throws ClientException
     */
    public function deleteIndex(array $params): array
    {
        try {
            return $this->getArrayResponse($this->client->indices()->delete($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Failed to delete index');
        }
    }

    /**
     * @throws ClientException
     */
    public function existsIndexAlias(array $params): bool
    {
        try {
            return $this->getBoolResponse($this->client->indices()->existsAlias($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithBool($exception, 'Failed to check if Alias exists');
        }
    }

    /**
     * @throws ClientException
     */
    public function getIndexAlias(array $params): array
    {
        try {
            return $this->getArrayResponse($this->client->indices()->getAlias($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Failed to get an Alias');
        }
    }

    /**
     * @throws ClientException
     */
    public function deleteIndexAlias(array $params): array
    {
        try {
            return $this->getArrayResponse($this->client->indices()->deleteAlias($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Failed to delete an Alias');
        }
    }

    /**
     * @throws ClientException
     */
    public function getAllIndexAliases(array $params): array
    {
        try {
            $params = array_merge($params, ['format' => 'json']);

            return $this->getArrayResponse($this->client->cat()->aliases($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Failed to get all index Aliases');
        }
    }

    /**
     * @throws ClientException
     */
    public function updateIndexAliases(array $params): array
    {
        try {
            return $this->getArrayResponse($this->client->indices()->updateAliases($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Failed to update Aliases');
        }
    }

    /**
     * @throws ClientException
     */
    public function putIndexMapping(array $params): array
    {
        try {
            return $this->getArrayResponse($this->client->indices()->putMapping($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Failed to put mapping');
        }
    }

    /**
     * @throws ClientException
     */
    public function getIndexMapping(array $params): array
    {
        try {
            return $this->getArrayResponse($this->client->indices()->getMapping($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Failed to get mapping');
        }
    }

    /**
     * @throws ClientException
     */
    public function getIndexSettings(array $params): array
    {
        try {
            return $this->getArrayResponse($this->client->indices()->getSettings($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Failed to get index settings');
        }
    }

    /**
     * @throws ClientException
     */
    public function putIndexSettings(array $params): array
    {
        try {
            return $this->getArrayResponse($this->client->indices()->putSettings($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Failed to update index settings');
        }
    }

    /**
     * @throws ClientException
     */
    public function getIndexStats(array $params): array
    {
        try {
            return $this->getArrayResponse($this->client->indices()->stats($params));
        } catch (Exception $exception) {
            return $this->handleExceptionsWithArray($exception, 'Failed to get index stats');
        }
    }

    /**
     * @throws ClientException
     */
    private function handleExceptionsWIthBool(Exception $exception, string $message): bool
    {
        if ($exception instanceof ClientResponseException && $exception->getCode() === 404) {
            return false;
        }

        throw new ClientException(
            sprintf('%s :%s', $message, $exception->getMessage())
        );
    }

    /**
     * @throws ClientException
     */
    private function handleExceptionsWithArray(Exception $exception, string $message): array
    {
        if ($exception instanceof ClientResponseException && $exception->getCode() === 404) {
            return [];
        }

        throw new ClientException(
            sprintf('%s :%s', $message, $exception->getMessage())
        );
    }

    private function getArrayResponse(Elasticsearch $response): array
    {
        return $response->asArray();
    }

    private function getBoolResponse(Elasticsearch $response): bool
    {
        return $response->asBool();
    }
}
