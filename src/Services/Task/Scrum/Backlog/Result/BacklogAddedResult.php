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
use Bitrix24\SDK\Core\Result\AbstractResult;

/**
 * Class BacklogAddedResult
 *
 * Represents the result of adding a backlog
 *
 * @package Bitrix24\SDK\Services\Task\Scrum\Backlog\Result
 */
class BacklogAddedResult extends AbstractResult implements AddedItemIdResultInterface
{
    /**
     * Get the ID of the added backlog
     *
     * @throws BaseException
     */
    public function getId(): int
    {
        $result = $this->getCoreResponse()->getResponseData()->getResult();

        if (isset($result['id'])) {
            return (int)$result['id'];
        }

        throw new BaseException('Unable to get backlog ID from response');
    }

    /**
     * Get the full backlog data from the response
     *
     * @throws BaseException
     */
    public function getBacklog(): BacklogItemResult
    {
        $result = $this->getCoreResponse()->getResponseData()->getResult();
        return new BacklogItemResult($result);
    }
}
