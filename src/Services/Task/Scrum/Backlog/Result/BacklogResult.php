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

use Bitrix24\SDK\Core\Exceptions\BaseException;
use Bitrix24\SDK\Core\Result\AbstractResult;

/**
 * Class BacklogResult
 *
 * @package Bitrix24\SDK\Services\Task\Scrum\Backlog\Result
 */
class BacklogResult extends AbstractResult
{
    /**
     * @throws BaseException
     */
    public function backlog(): BacklogItemResult
    {
        return new BacklogItemResult($this->getCoreResponse()->getResponseData()->getResult());
    }
}
