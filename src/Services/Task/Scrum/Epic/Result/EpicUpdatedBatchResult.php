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

namespace Bitrix24\SDK\Services\Task\Scrum\Epic\Result;

use Bitrix24\SDK\Core\Contracts\UpdatedItemResultInterface;
use Bitrix24\SDK\Core\Exceptions\BaseException;
use Bitrix24\SDK\Core\Response\DTO\ResponseData;

/**
 * Class EpicUpdatedBatchResult
 *
 * Represents the result of batch updating epics
 *
 * @package Bitrix24\SDK\Services\Task\Scrum\Epic\Result
 */
class EpicUpdatedBatchResult implements UpdatedItemResultInterface
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
        
        if (is_array($result) && isset($result['id'])) {
            // If we get epic data back, it means update was successful
            return true;
        }
        
        // Default to false if we can't determine success
        return false;
    }

    /**
     * Get the updated epic data if available in batch response
     *
     * @throws BaseException
     */
    public function getEpic(): ?EpicItemResult
    {
        $result = $this->getResponseData()->getResult();
        
        if (is_array($result) && isset($result['id'])) {
            return new EpicItemResult($result);
        }
        
        return null;
    }
}