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
use Bitrix24\SDK\Core\Result\AbstractResult;

/**
 * Class BacklogDeletedResult
 *
 * Represents the result of deleting a backlog
 *
 * @package Bitrix24\SDK\Services\Task\Scrum\Backlog\Result
 */
class BacklogDeletedResult extends AbstractResult implements DeletedItemResultInterface
{
    /**
     * Check if the delete operation was successful
     *
     * @throws BaseException
     */
    public function isSuccess(): bool
    {
        $result = $this->getCoreResponse()->getResponseData()->getResult();

        // For Backlog delete, successful deletion returns an empty array
        return $result === [];
    }
}
