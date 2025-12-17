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

use Bitrix24\SDK\Core\Result\AbstractItem;

/**
 * Class EpicItemResult
 *
 * @property-read int|null $id Epic identifier
 * @property-read string|null $name Epic name
 * @property-read string|null $description Epic description
 * @property-read int|null $groupId Identifier of the group (scrum) to which the epic belongs
 * @property-read string|null $color Epic color
 * @property-read array|null $files Array of files attached to the epic
 * @property-read int|null $createdBy Created by user identifier
 * @property-read int|null $modifiedBy Modified by user identifier
 */
class EpicItemResult extends AbstractItem
{
}
