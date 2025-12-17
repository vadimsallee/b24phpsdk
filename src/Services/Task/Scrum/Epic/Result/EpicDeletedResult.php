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
use Bitrix24\SDK\Core\Result\AbstractResult;

/**
 * Class EpicDeletedResult
 *
 * Represents the result of deleting an epic
 *
 * @package Bitrix24\SDK\Services\Task\Scrum\Epic\Result
 */
class EpicDeletedResult extends AbstractResult implements DeletedItemResultInterface
{
    /**
     * Check if the delete operation was successful
     *
     * @throws BaseException
     */
    public function isSuccess(): bool
    {
        $result = $this->getCoreResponse()->getResponseData()->getResult();
        
        // For Epic delete, successful deletion returns an empty array
        return is_array($result) && empty($result);
    }

}
