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

namespace Bitrix24\SDK\Services\Lists\Field\Result;

use Bitrix24\SDK\Core\Response\ResponseData;
use Bitrix24\SDK\Core\Result\AbstractResult;

/**
 * Class UpdatedFieldBatchResult
 *
 * @package Bitrix24\SDK\Services\Lists\Field\Result
 */
class UpdatedFieldBatchResult extends AbstractResult
{
    /**
     * Get response data
     *
     * @return ResponseData
     */
    public function getResponseData(): ResponseData
    {
        return $this->getCoreResponse()->getResponseData();
    }
    
    /**
     * Check if update operation was successful
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        $result = $this->getCoreResponse()->getResponseData()->getResult();
        return (bool) $result;
    }
}