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

namespace Bitrix24\SDK\Services\Lists\Lists\Result;

use Bitrix24\SDK\Core\Exceptions\BaseException;
use Bitrix24\SDK\Core\Result\AbstractResult;

/**
 * Class ListResult
 *
 * @package Bitrix24\SDK\Services\Lists\Lists\Result
 */
class ListResult extends AbstractResult
{
    /**
     * @throws BaseException
     */
    public function list(): ListItemResult
    {
        return new ListItemResult($this->getCoreResponse()->getResponseData()->getResult());
    }
}
