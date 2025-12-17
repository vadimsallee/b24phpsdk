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

use Bitrix24\SDK\Core\Contracts\DeletedItemResultInterface;
use Bitrix24\SDK\Core\Exceptions\BaseException;
use Bitrix24\SDK\Core\Response\DTO\ResponseData;

/**
 * Class EpicDeletedBatchResult
 *
 * Represents the result of batch deleting epics
 *
 * @package Bitrix24\SDK\Services\Task\Scrum\Epic\Result
 */
class EpicDeletedBatchResult implements DeletedItemResultInterface
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
        
        // For Epic delete, successful deletion returns an empty array
        if (is_array($result) && empty($result)) {
            return true;
        }
        
        // Default to false if we can't determine success
        return false;
    }
}