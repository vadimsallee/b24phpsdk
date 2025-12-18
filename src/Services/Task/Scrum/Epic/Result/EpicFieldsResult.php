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
 * Class EpicFieldsResult
 *
 * Represents the result of Epic fields request
 *
 * @package Bitrix24\SDK\Services\Task\Scrum\Epic\Result
 */
class EpicFieldsResult extends AbstractResult
{
    /**
     * Get Epic fields description
     * Returns the fields array directly, without the nested 'fields' key
     *
     * @throws BaseException
     */
    public function getFieldsDescription(): array
    {
        $result = $this->getCoreResponse()->getResponseData()->getResult();

        // Epic API must return fields nested under 'fields' key
        if (!is_array($result)) {
            throw new BaseException('Epic fields API returned invalid response format');
        }

        if (!isset($result['fields']) || !is_array($result['fields'])) {
            throw new BaseException('Epic fields API response is missing fields data');
        }

        $fields = $result['fields'];

        // Temporary workaround: API doesn't return 'id' field description, so we add it manually
        if (!isset($fields['id'])) {
            $fields['id'] = [
                'type' => 'integer',
            ];
        }

        return $fields;
    }
}
