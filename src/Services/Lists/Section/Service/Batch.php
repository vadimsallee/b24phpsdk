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

namespace Bitrix24\SDK\Services\Lists\Section\Service;

use Bitrix24\SDK\Attributes\ApiBatchMethodMetadata;
use Bitrix24\SDK\Attributes\ApiBatchServiceMetadata;
use Bitrix24\SDK\Core\Contracts\BatchOperationsInterface;
use Bitrix24\SDK\Core\Credentials\Scope;
use Bitrix24\SDK\Core\Exceptions\BaseException;
use Bitrix24\SDK\Core\Result\AddedItemBatchResult;
use Bitrix24\SDK\Core\Result\DeletedItemBatchResult;
use Bitrix24\SDK\Core\Result\UpdatedItemBatchResult;
use Bitrix24\SDK\Services\AbstractBatchService;
use Bitrix24\SDK\Services\Lists\Section\Result\SectionItemResult;
use Generator;
use Psr\Log\LoggerInterface;

#[ApiBatchServiceMetadata(new Scope(['lists']))]
class Batch extends AbstractBatchService
{
    /**
     * Batch constructor.
     */
    public function __construct(BatchOperationsInterface $batch, LoggerInterface $log)
    {
        parent::__construct($batch, $log);
    }

    /**
     * Batch list method for list sections
     *
     * @param string $iblockTypeId Information block type identifier
     * @param int|string $iblockId Information block identifier or code
     * @param array $filter Filter for sections
     * @param array $select Fields to select
     * @param string|null $iblockCode Information block code
     *
     * @return Generator<int, SectionItemResult, mixed, mixed>
     *
     * @throws BaseException
     */
    #[ApiBatchMethodMetadata(
        'lists.section.get',
        'https://apidocs.bitrix24.com/api-reference/lists/sections/lists-section-get.html',
        'Returns a section or a list of sections.'
    )]
    public function list(
        string $iblockTypeId,
        int|string $iblockId,
        array $filter = [],
        array $select = [],
        ?string $iblockCode = null
    ): Generator {
        $params = [
            'IBLOCK_TYPE_ID' => $iblockTypeId,
        ];

        if (is_int($iblockId)) {
            $params['IBLOCK_ID'] = $iblockId;
        } else {
            $params['IBLOCK_CODE'] = $iblockId;
        }

        if ($iblockCode !== null) {
            $params['IBLOCK_CODE'] = $iblockCode;
        }

        if ($filter !== []) {
            $params['FILTER'] = $filter;
        }

        if ($select !== []) {
            $params['SELECT'] = $select;
        }

        foreach ($this->batch->getTraversableList('lists.section.get', [], [], [], null, $params) as $key => $value) {
            yield $key => new SectionItemResult($value);
        }
    }

    /**
     * Batch add method for list sections
     *
     * @param array<array{IBLOCK_TYPE_ID: string, IBLOCK_ID?: int, IBLOCK_CODE?: string, SECTION_CODE: string, FIELDS: array, IBLOCK_SECTION_ID?: int}> $sections Array of section data
     *
     * @return Generator<int, AddedItemBatchResult, mixed, mixed>
     *
     * @throws BaseException
     */
    #[ApiBatchMethodMetadata(
        'lists.section.add',
        'https://apidocs.bitrix24.com/api-reference/lists/sections/lists-section-add.html',
        'Creates list sections.'
    )]
    public function add(array $sections): Generator
    {
        foreach ($this->batch->addEntityItems('lists.section.add', $sections) as $key => $item) {
            yield $key => new AddedItemBatchResult($item);
        }
    }

    /**
     * Batch update method for list sections
     *
     * Update elements in array with structure:
     * element_id => [
     *  'IBLOCK_TYPE_ID' => string,
     *  'IBLOCK_ID' => int,              // or use IBLOCK_CODE
     *  'SECTION_ID' => int,             // or use SECTION_CODE
     *  'FIELDS' => [],                  // Section fields to update
     *  'IBLOCK_CODE' => string,         // optional
     *  'SECTION_CODE' => string         // optional
     * ]
     *
     * @param array $sections Array of section data with IDs as keys
     *
     * @return Generator<int, UpdatedItemBatchResult, mixed, mixed>
     *
     * @throws BaseException
     */
    #[ApiBatchMethodMetadata(
        'lists.section.update',
        'https://apidocs.bitrix24.com/api-reference/lists/sections/lists-section-update.html',
        'Updates list sections.'
    )]
    public function update(array $sections): Generator
    {
        foreach ($this->batch->updateEntityItems('lists.section.update', $sections) as $key => $item) {
            yield $key => new UpdatedItemBatchResult($item);
        }
    }

    /**
     * Batch delete method for list sections
     *
     * @param array<array{IBLOCK_TYPE_ID: string, IBLOCK_ID?: int, IBLOCK_CODE?: string, SECTION_ID?: int, SECTION_CODE?: string}> $sections Array of section parameters for deletion
     *
     * @return Generator<int, DeletedItemBatchResult, mixed, mixed>
     *
     * @throws BaseException
     */
    #[ApiBatchMethodMetadata(
        'lists.section.delete',
        'https://apidocs.bitrix24.com/api-reference/lists/sections/lists-section-delete.html',
        'Deletes list sections.'
    )]
    public function delete(array $sections): Generator
    {
        foreach ($this->batch->deleteEntityItems('lists.section.delete', $sections) as $key => $item) {
            yield $key => new DeletedItemBatchResult($item);
        }
    }
}
