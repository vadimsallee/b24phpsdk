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

namespace Bitrix24\SDK\Services\Task\Scrum\Backlog\Result;

use Bitrix24\SDK\Core\Contracts\AddedItemIdResultInterface;
use Bitrix24\SDK\Core\Exceptions\BaseException;
use Bitrix24\SDK\Core\Response\DTO\ResponseData;

/**
 * Class BacklogAddedBatchResult
 *
 * Represents the result of batch adding backlog items
 *
 * @package Bitrix24\SDK\Services\Task\Scrum\Backlog\Result
 */
class BacklogAddedBatchResult implements AddedItemIdResultInterface
{
    public function __construct(private readonly ResponseData $responseData)
    {
    }

    public function getResponseData(): ResponseData
    {
        return $this->responseData;
    }

    /**
     * Get the ID of the added backlog item
     *
     * @throws BaseException
     */
    public function getId(): int
    {
        $result = $this->getResponseData()->getResult();

        if (isset($result['id'])) {
            return (int)$result['id'];
        }

        throw new BaseException('Unable to get backlog item ID from batch response');
    }

    /**
     * Get the full backlog item data from the batch response
     *
     * @throws BaseException
     */
    public function getBacklogItem(): BacklogItemResult
    {
        $result = $this->getResponseData()->getResult();
        return new BacklogItemResult($result);
    }
}
