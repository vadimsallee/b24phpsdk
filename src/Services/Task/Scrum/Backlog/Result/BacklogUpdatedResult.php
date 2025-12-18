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
use Bitrix24\SDK\Core\Result\AbstractResult;

/**
 * Class BacklogUpdatedResult
 *
 * Represents the result of updating a backlog
 *
 * @package Bitrix24\SDK\Services\Task\Scrum\Backlog\Result
 */
class BacklogUpdatedResult extends AbstractResult implements UpdatedItemResultInterface
{
    /**
     * Check if the update operation was successful
     *
     * @throws BaseException
     */
    public function isSuccess(): bool
    {
        $result = $this->getCoreResponse()->getResponseData()->getResult();
        // If we get backlog data back, it means update was successful
        return is_array($result) && isset($result['id']);
    }

    /**
     * Get the updated backlog data from the response
     *
     * @throws BaseException
     */
    public function getBacklog(): BacklogItemResult
    {
        $result = $this->getCoreResponse()->getResponseData()->getResult();
        return new BacklogItemResult($result);
    }
}
