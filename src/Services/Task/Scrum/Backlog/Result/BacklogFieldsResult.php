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
 * Class BacklogFieldsResult
 *
 * Represents the result of Backlog fields request
 *
 * @package Bitrix24\SDK\Services\Task\Scrum\Backlog\Result
 */
class BacklogFieldsResult extends AbstractResult
{
    /**
     * Get Backlog fields description
     * Returns the fields array directly, without the nested 'fields' key
     *
     * @throws BaseException
     */
    public function getFieldsDescription(): array
    {
        $result = $this->getCoreResponse()->getResponseData()->getResult();

        // Backlog API must return fields nested under 'fields' key
        if (!is_array($result)) {
            throw new BaseException('Backlog fields API returned invalid response format');
        }
        
        $fields = $result['fields'];
        
        if (!isset($fields['id'])) {
            $fields['id'] = [
                'type' => 'integer',
            ];
        }

        return $fields;
    }
}
