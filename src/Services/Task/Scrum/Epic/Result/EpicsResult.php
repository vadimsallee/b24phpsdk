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

use Bitrix24\SDK\Core\Exceptions\BaseException;
use Bitrix24\SDK\Core\Result\AbstractResult;

/**
 * Class EpicsResult
 *
 * @package Bitrix24\SDK\Services\Task\Scrum\Epic\Result
 */
class EpicsResult extends AbstractResult
{
    /**
     * @return EpicItemResult[]
     * @throws BaseException
     */
    public function getEpics(): array
    {
        $epics = [];
        foreach ($this->getCoreResponse()->getResponseData()->getResult() as $item) {
            $epics[] = new EpicItemResult($item);
        }

        return $epics;
    }
}
