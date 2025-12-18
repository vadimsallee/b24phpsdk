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

use Bitrix24\SDK\Core\Contracts\DeletedItemResultInterface;
use Bitrix24\SDK\Core\Exceptions\BaseException;
use Bitrix24\SDK\Core\Response\DTO\ResponseData;

/**
 * Class BacklogDeletedBatchResult
 *
 * Represents the result of batch deleting backlog items
 *
 * @package Bitrix24\SDK\Services\Task\Scrum\Backlog\Result
 */
class BacklogDeletedBatchResult implements DeletedItemResultInterface
{
    public function __construct(private readonly ResponseData $responseData)
    {
    }

    public function getResponseData(): ResponseData
    {
        return $this->responseData;
    }

    /**
     * Check if the delete operation was successful
     *
     * @throws BaseException
     */
    public function isSuccess(): bool
    {
        $result = $this->getResponseData()->getResult();
        // For Backlog delete, successful deletion returns an empty array
        // Default to false if we can't determine success
        return $result === [];
    }
}
