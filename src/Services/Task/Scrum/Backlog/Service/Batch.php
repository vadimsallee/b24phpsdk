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

namespace Bitrix24\SDK\Services\Task\Scrum\Backlog\Service;

use Bitrix24\SDK\Attributes\ApiBatchMethodMetadata;
use Bitrix24\SDK\Attributes\ApiBatchServiceMetadata;
use Bitrix24\SDK\Core\Credentials\Scope;
use Bitrix24\SDK\Core\Exceptions\BaseException;
use Bitrix24\SDK\Services\Task\Scrum\Backlog\Result\BacklogAddedBatchResult;
use Bitrix24\SDK\Services\Task\Scrum\Backlog\Result\BacklogDeletedBatchResult;
use Bitrix24\SDK\Services\Task\Scrum\Backlog\Result\BacklogItemResult;
use Bitrix24\SDK\Services\Task\Scrum\Backlog\Result\BacklogUpdatedBatchResult;
use Bitrix24\SDK\Services\Task\Scrum\Backlog\Batch as BacklogBatch;
use Generator;
use Psr\Log\LoggerInterface;

#[ApiBatchServiceMetadata(new Scope(['task']))]
class Batch
{
    public function __construct(protected BacklogBatch $batch, protected LoggerInterface $log)
    {
    }

    /**
     * Batch add method for backlog items
     *
     * @param array<array> $backlogItems Array of backlog item data
     *
     * @return Generator<int, BacklogAddedBatchResult, mixed, mixed>
     *
     * @throws BaseException
     */
    #[ApiBatchMethodMetadata(
        'tasks.api.scrum.backlog.add',
        'https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/backlog/tasks-api-scrum-backlog-add.html',
        'Adds backlog items to Scrum.'
    )]
    public function add(array $backlogItems): Generator
    {
        foreach ($this->batch->addEntityItems('tasks.api.scrum.backlog.add', $backlogItems) as $key => $item) {
            yield $key => new BacklogAddedBatchResult($item);
        }
    }

    /**
     * Batch update method for backlog items
     *
     * Update elements in array with structure
     * element_id => [  // backlog item id
     *  'fields' => [] // backlog item fields to update
     * ]
     *
     * @param array<int, array> $backlogItems Array of backlog item data with id as key and fields
     *
     * @return Generator<int, BacklogUpdatedBatchResult, mixed, mixed>
     *
     * @throws BaseException
     */
    #[ApiBatchMethodMetadata(
        'tasks.api.scrum.backlog.update',
        'https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/backlog/tasks-api-scrum-backlog-update.html',
        'Updates backlog items in Scrum.'
    )]
    public function update(array $backlogItems): Generator
    {
        foreach ($this->batch->updateEntityItems('tasks.api.scrum.backlog.update', $backlogItems) as $key => $item) {
            yield $key => new BacklogUpdatedBatchResult($item);
        }
    }

    /**
     * Batch delete method for backlog items
     *
     * @param int[] $backlogItemIds Array of backlog item identifiers
     *
     * @return Generator<int, BacklogDeletedBatchResult, mixed, mixed>
     *
     * @throws BaseException
     */
    #[ApiBatchMethodMetadata(
        'tasks.api.scrum.backlog.delete',
        'https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/backlog/tasks-api-scrum-backlog-delete.html',
        'Deletes backlog items from Scrum.'
    )]
    public function delete(array $backlogItemIds): Generator
    {
        foreach ($this->batch->deleteEntityItems('tasks.api.scrum.backlog.delete', $backlogItemIds) as $key => $item) {
            yield $key => new BacklogDeletedBatchResult($item);
        }
    }

    /**
     * Batch list method for backlog items
     *
     * @param array $order  Sort order
     * @param array $filter Filter criteria
     * @param array $select Fields to select
     * @param int|null $limit Number of items to select
     *
     * @return Generator<int, BacklogItemResult, mixed, mixed>
     *
     * @throws BaseException
     */
    #[ApiBatchMethodMetadata(
        'tasks.api.scrum.backlog.list',
        'https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/backlog/tasks-api-scrum-backlog-list.html',
        'Retrieves a list of backlog items from Scrum.'
    )]
    public function list(array $order = [], array $filter = [], array $select = [], ?int $limit = null): Generator
    {
        foreach ($this->batch->getTraversableList('tasks.api.scrum.backlog.list', $order, $filter, $select, $limit) as $key => $value) {
            yield $key => new BacklogItemResult($value);
        }
    }
}
