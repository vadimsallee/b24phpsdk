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

namespace Bitrix24\SDK\Services\Task\Scrum\Epic\Service;

use Bitrix24\SDK\Attributes\ApiBatchMethodMetadata;
use Bitrix24\SDK\Attributes\ApiBatchServiceMetadata;
use Bitrix24\SDK\Core\Credentials\Scope;
use Bitrix24\SDK\Core\Exceptions\BaseException;
use Bitrix24\SDK\Services\Task\Scrum\Epic\Result\EpicAddedBatchResult;
use Bitrix24\SDK\Services\Task\Scrum\Epic\Result\EpicDeletedBatchResult;
use Bitrix24\SDK\Services\Task\Scrum\Epic\Result\EpicItemResult;
use Bitrix24\SDK\Services\Task\Scrum\Epic\Result\EpicUpdatedBatchResult;
use Bitrix24\SDK\Services\Task\Scrum\Epic\Batch as EpicBatch;
use Generator;
use Psr\Log\LoggerInterface;

#[ApiBatchServiceMetadata(new Scope(['task']))]
class Batch
{
    public function __construct(protected EpicBatch $batch, protected LoggerInterface $log)
    {
    }

    /**
     * Batch add method for epics
     *
     * @param array<array> $epics Array of epic data
     *
     * @return Generator<int, EpicAddedBatchResult, mixed, mixed>
     *
     * @throws BaseException
     */
    #[ApiBatchMethodMetadata(
        'tasks.api.scrum.epic.add',
        'https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/epic/tasks-api-scrum-epic-add.html',
        'Adds epics to Scrum.'
    )]
    public function add(array $epics): Generator
    {
        foreach ($this->batch->addEntityItems('tasks.api.scrum.epic.add', $epics) as $key => $item) {
            yield $key => new EpicAddedBatchResult($item);
        }
    }

    /**
     * Batch update method for epics
     *
     * Update elements in array with structure
     * element_id => [  // epic id
     *  'fields' => [] // epic fields to update
     * ]
     *
     * @param array<int, array> $epics Array of epic data with id as key and fields
     *
     * @return Generator<int, EpicUpdatedBatchResult, mixed, mixed>
     *
     * @throws BaseException
     */
    #[ApiBatchMethodMetadata(
        'tasks.api.scrum.epic.update',
        'https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/epic/tasks-api-scrum-epic-update.html',
        'Updates epics in Scrum.'
    )]
    public function update(array $epics): Generator
    {
        foreach ($this->batch->updateEntityItems('tasks.api.scrum.epic.update', $epics) as $key => $item) {
            yield $key => new EpicUpdatedBatchResult($item);
        }
    }

    /**
     * Batch delete method for epics
     *
     * @param int[] $epicIds Array of epic identifiers
     *
     * @return Generator<int, EpicDeletedBatchResult, mixed, mixed>
     *
     * @throws BaseException
     */
    #[ApiBatchMethodMetadata(
        'tasks.api.scrum.epic.delete',
        'https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/epic/tasks-api-scrum-epic-delete.html',
        'Deletes epics from Scrum.'
    )]
    public function delete(array $epicIds): Generator
    {
        foreach ($this->batch->deleteEntityItems('tasks.api.scrum.epic.delete', $epicIds) as $key => $item) {
            yield $key => new EpicDeletedBatchResult($item);
        }
    }

    /**
     * Batch list method for epics
     *
     * @param array $order  Sort order
     * @param array $filter Filter criteria
     * @param array $select Fields to select
     * @param int|null $limit Number of items to select
     *
     * @return Generator<int, EpicItemResult, mixed, mixed>
     *
     * @throws BaseException
     */
    #[ApiBatchMethodMetadata(
        'tasks.api.scrum.epic.list',
        'https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/epic/tasks-api-scrum-epic-list.html',
        'Retrieves a list of epics from Scrum.'
    )]
    public function list(array $order = [], array $filter = [], array $select = [], ?int $limit = null): Generator
    {
        foreach ($this->batch->getTraversableList('tasks.api.scrum.epic.list', $order, $filter, $select, $limit) as $key => $value) {
            yield $key => new EpicItemResult($value);
        }
    }
}
