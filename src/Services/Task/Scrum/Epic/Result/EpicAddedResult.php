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
use Bitrix24\SDK\Core\Result\AbstractResult;

/**
 * Class EpicAddedResult
 *
 * Represents the result of adding an epic
 *
 * @package Bitrix24\SDK\Services\Task\Scrum\Epic\Result
 */
class EpicAddedResult extends AbstractResult implements AddedItemIdResultInterface
{
    /**
     * Get the ID of the added epic
     *
     * @throws BaseException
     */
    public function getId(): int
    {
        $result = $this->getCoreResponse()->getResponseData()->getResult();
        
        if (is_array($result) && isset($result['id'])) {
            return (int)$result['id'];
        }
        
        throw new BaseException('Unable to get epic ID from response');
    }

    /**
     * Get the full epic data from the response
     *
     * @throws BaseException
     */
    public function getEpic(): EpicItemResult
    {
        $result = $this->getCoreResponse()->getResponseData()->getResult();
        
        if (is_array($result)) {
            return new EpicItemResult($result);
        }
        
        throw new BaseException('Unable to get epic data from response');
    }
}
