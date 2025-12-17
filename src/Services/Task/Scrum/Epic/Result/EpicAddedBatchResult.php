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

use Bitrix24\SDK\Core\Contracts\AddedItemIdResultInterface;
use Bitrix24\SDK\Core\Exceptions\BaseException;
use Bitrix24\SDK\Core\Response\DTO\ResponseData;

/**
 * Class EpicAddedBatchResult
 *
 * Represents the result of batch adding epics
 *
 * @package Bitrix24\SDK\Services\Task\Scrum\Epic\Result
 */
class EpicAddedBatchResult implements AddedItemIdResultInterface
{
    public function __construct(private readonly ResponseData $responseData)
    {
    }

    public function getResponseData(): ResponseData
    {
        return $this->responseData;
    }

    /**
     * Get the ID of the added epic
     *
     * @throws BaseException
     */
    public function getId(): int
    {
        $result = $this->getResponseData()->getResult();
        
        if (is_array($result) && isset($result['id'])) {
            return (int)$result['id'];
        }
        
        throw new BaseException('Unable to get epic ID from batch response');
    }

    /**
     * Get the full epic data from the batch response
     *
     * @throws BaseException
     */
    public function getEpic(): EpicItemResult
    {
        $result = $this->getResponseData()->getResult();
        
        if (is_array($result)) {
            return new EpicItemResult($result);
        }
        
        throw new BaseException('Unable to get epic data from batch response');
    }
}
