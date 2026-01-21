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

namespace Bitrix24\SDK\Services\Lists\Section\Result;

use Bitrix24\SDK\Core\Result\AbstractItem;
use Carbon\CarbonImmutable;

/**
 * Class SectionItemResult
 *
 * @property-read int $ID
 * @property-read string $CODE
 * @property-read string $XML_ID
 * @property-read string $EXTERNAL_ID
 * @property-read ?int $IBLOCK_SECTION_ID
 * @property-read CarbonImmutable $TIMESTAMP_X
 * @property-read int $SORT
 * @property-read string $NAME
 * @property-read bool $ACTIVE               // Y/N -> bool
 * @property-read bool $GLOBAL_ACTIVE        // Y/N -> bool
 * @property-read int $LEFT_MARGIN
 * @property-read int $RIGHT_MARGIN
 * @property-read int $DEPTH_LEVEL
 * @property-read string $SEARCHABLE_CONTENT
 * @property-read int $MODIFIED_BY
 * @property-read CarbonImmutable $DATE_CREATE
 * @property-read int $CREATED_BY
 */
class SectionItemResult extends AbstractItem
{
    public function __get($offset)
    {
        return match ($offset) {
            'ID' => $this->data[$offset] ? (int)$this->data[$offset] : null,
            'SORT', 'LEFT_MARGIN', 'RIGHT_MARGIN', 'DEPTH_LEVEL', 'MODIFIED_BY', 'CREATED_BY' => $this->data[$offset] ? (int)$this->data[$offset] : null,
            'IBLOCK_SECTION_ID' => $this->data[$offset] !== null ? (int)$this->data[$offset] : null,
            'ACTIVE', 'GLOBAL_ACTIVE' => ($this->data[$offset] ?? 'N') === 'Y',
            'TIMESTAMP_X' => isset($this->data[$offset]) ? CarbonImmutable::createFromFormat('m/d/Y h:i:s a', $this->data[$offset]) : null,
            'DATE_CREATE' => isset($this->data[$offset]) ? CarbonImmutable::createFromFormat('m/d/Y h:i:s a', $this->data[$offset]) : null,
            default => $this->data[$offset] ?? null,
        };
    }
}
