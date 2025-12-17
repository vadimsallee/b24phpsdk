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
use Bitrix24\SDK\Core\Result\AbstractResult;

/**
 * Class EpicUpdatedResult
 *
 * Represents the result of updating an epic
 *
 * @package Bitrix24\SDK\Services\Task\Scrum\Epic\Result
 */
class EpicUpdatedResult extends AbstractResult implements UpdatedItemResultInterface
{
    /**
     * Check if the update operation was successful
     *
     * @throws BaseException
     */
    public function isSuccess(): bool
    {
        $result = $this->getCoreResponse()->getResponseData()->getResult();
        
        if (is_array($result) && isset($result['id'])) {
            // If we get epic data back, it means update was successful
            return true;
        }
        
        // Check HTTP response status as fallback
        return $this->getCoreResponse()->getHttpResponse()->getStatusCode() === 200;
    }

    /**
     * Get the updated epic data if available in response
     *
     * @throws BaseException
     */
    public function getEpic(): EpicItemResult
    {
        $result = $this->getCoreResponse()->getResponseData()->getResult();
        
        if (is_array($result) && isset($result['id'])) {
            return new EpicItemResult($result);
        }
        
        throw new BaseException('Unable to get updated epic data from response');
    }
}
