<?php

/**
 * This file is part of the bitrix24-php-sdk package.
 *
 * © Sally Fancen <vadimsallee@gmail.com>
 *
 * For the full copyright and license information, please view the MIT-LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bitrix24\SDK\Services\Task\Scrum\Backlog;

use Bitrix24\SDK\Core\Exceptions\BaseException;
use Bitrix24\SDK\Core\Exceptions\InvalidArgumentException;
use Bitrix24\SDK\Core\Response\DTO\ResponseData;
use Generator;

/**
 * Class Batch
 *
 * @package Bitrix24\SDK\Services\Task\Scrum\Backlog
 */
class Batch extends \Bitrix24\SDK\Core\Batch
{
    /**
     * Add entity items with batch call for backlog items
     * Backlog API expects 'fields' parameter structure for add operation
     *
     * @return Generator<int, ResponseData>|ResponseData[]
     * @throws BaseException
     */
    public function addEntityItems(string $apiMethod, array $entityItems): Generator
    {
        $this->logger->debug(
            'addEntityItems.start',
            [
                'apiMethod' => $apiMethod,
                'entityItems' => $entityItems,
            ]
        );

        try {
            $this->clearCommands();
            foreach ($entityItems as $cnt => $item) {
                // For backlog.add API, the structure should be:
                // { "groupId": 123, "fields": {} }
                // If the item contains 'groupId' directly, restructure it
                if (isset($item['groupId']) && !isset($item['fields'])) {
                    $groupId = $item['groupId'];
                    unset($item['groupId']);
                    $apiParams = [
                        'groupId' => $groupId,
                        'fields' => $item
                    ];
                } else {
                    // Item already has correct structure
                    $apiParams = $item;
                }
                
                $this->registerCommand($apiMethod, $apiParams);
            }

            foreach ($this->getTraversable(true) as $cnt => $addedItemResult) {
                yield $cnt => $addedItemResult;
            }
        } catch (\Throwable $throwable) {
            $errorMessage = sprintf('batch add backlog items: %s', $throwable->getMessage());
            $this->logger->error(
                $errorMessage,
                [
                    'trace' => $throwable->getTrace(),
                ]
            );

            throw new BaseException($errorMessage, $throwable->getCode(), $throwable);
        }

        $this->logger->debug('addEntityItems.finish');
    }

    /**
     * Update entity items with batch call for backlog items
     * Backlog API expects 'id' and 'fields' parameters
     *
     * @return Generator<int, ResponseData>|ResponseData[]
     * @throws BaseException
     */
    public function updateEntityItems(string $apiMethod, array $entityItems, ?array $additionalParameters = null): Generator
    {
        $this->logger->debug(
            'updateEntityItems.start',
            [
                'apiMethod' => $apiMethod,
                'entityItems' => $entityItems,
                'additionalParameters' => $additionalParameters,
            ]
        );

        try {
            $this->clearCommands();
            foreach ($entityItems as $itemId => $item) {
                if (!is_int($itemId)) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'invalid type «%s» of backlog item id «%s», backlog item id must be integer type',
                            gettype($itemId),
                            $itemId
                        )
                    );
                }

                // For backlog.update API, use 'id' parameter (not 'ID' or 'groupId')
                $apiParams = [
                    'id' => $itemId,
                    'fields' => $item['fields'] ?? $item
                ];

                if ($additionalParameters !== null) {
                    $apiParams = array_merge($apiParams, $additionalParameters);
                }

                $this->registerCommand($apiMethod, $apiParams);
            }

            foreach ($this->getTraversable(true) as $cnt => $updatedItemResult) {
                $this->logger->debug('updateEntityItems', ['result' => $updatedItemResult->getResult()]);
                yield $cnt => $updatedItemResult;
            }
        } catch (InvalidArgumentException $exception) {
            $errorMessage = sprintf('batch update backlog items: %s', $exception->getMessage());
            $this->logger->error(
                $errorMessage,
                [
                    'trace' => $exception->getTrace(),
                ]
            );
            throw $exception;
        } catch (\Throwable $exception) {
            $errorMessage = sprintf('batch update backlog items: %s', $exception->getMessage());
            $this->logger->error(
                $errorMessage,
                [
                    'trace' => $exception->getTrace(),
                ]
            );

            throw new BaseException($errorMessage, $exception->getCode(), $exception);
        }

        $this->logger->debug('updateEntityItems.finish');
    }

    /**
     * Delete entity items with batch call for backlog items
     *
     *
     * @return Generator<int, ResponseData>|ResponseData[]
     * @throws BaseException
     */
    public function deleteEntityItems(
        string $apiMethod,
        array $entityItemId,
        ?array $additionalParameters = null
    ): Generator {
        $this->logger->debug(
            'deleteEntityItems.start',
            [
                'apiMethod' => $apiMethod,
                'entityItems' => $entityItemId,
                'additionalParameters' => $additionalParameters,
            ]
        );

        try {
            $this->clearCommands();
            foreach ($entityItemId as $cnt => $itemId) {
                if (!is_int($itemId)) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'invalid type «%s» of backlog item id «%s» at position %s, backlog item id must be integer type',
                            gettype($itemId),
                            $itemId,
                            $cnt
                        )
                    );
                }

                // For backlog delete API, need to check what parameter is required
                // Try 'id' first, if that doesn't work, might need 'groupId' or 'ID'
                $apiParams = ['id' => $itemId];
                
                if ($additionalParameters !== null) {
                    $apiParams = array_merge($apiParams, $additionalParameters);
                }
                
                $this->registerCommand($apiMethod, $apiParams);
            }

            foreach ($this->getTraversable(true) as $cnt => $deletedItemResult) {
                $this->logger->debug('deleteEntityItems', ['result' => $deletedItemResult->getResult()]);
                yield $cnt => $deletedItemResult;
            }
        } catch (InvalidArgumentException $exception) {
            $errorMessage = sprintf('batch delete backlog items: %s', $exception->getMessage());
            $this->logger->error(
                $errorMessage,
                [
                    'trace' => $exception->getTrace(),
                ]
            );
            throw $exception;
        } catch (\Throwable $exception) {
            $errorMessage = sprintf('batch delete backlog items: %s', $exception->getMessage());
            $this->logger->error(
                $errorMessage,
                [
                    'trace' => $exception->getTrace(),
                ]
            );

            throw new BaseException($errorMessage, $exception->getCode(), $exception);
        }

        $this->logger->debug('deleteEntityItems.finish');
    }
}
