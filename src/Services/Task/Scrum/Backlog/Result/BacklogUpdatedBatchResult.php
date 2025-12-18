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

use Bitrix24\SDK\Core\Contracts\UpdatedItemResultInterface;
use Bitrix24\SDK\Core\Exceptions\BaseException;
use Bitrix24\SDK\Core\Response\DTO\ResponseData;

/**
 * Class BacklogUpdatedBatchResult
 *
 * Represents the result of batch updating backlog items
 *
 * @package Bitrix24\SDK\Services\Task\Scrum\Backlog\Result
 */
class BacklogUpdatedBatchResult implements UpdatedItemResultInterface
{
    public function __construct(private readonly ResponseData $responseData)
    {
    }

    public function getResponseData(): ResponseData
    {
        return $this->responseData;
    }

    /**
     * Check if the update operation was successful
     *
     * @throws BaseException
     */
    public function isSuccess(): bool
    {
        $result = $this->getResponseData()->getResult();
        // Default to false if we can't determine success
        // If we get backlog item data back, it means update was successful
        return is_array($result) && isset($result['id']);
    }

    /**
     * Get the updated backlog item data from the batch response
     *
     * @throws BaseException
     */
    public function getBacklogItem(): BacklogItemResult
    {
        $result = $this->getResponseData()->getResult();
        return new BacklogItemResult($result);
    }
}
