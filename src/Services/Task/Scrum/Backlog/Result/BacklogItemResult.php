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

use Bitrix24\SDK\Core\Result\AbstractItem;

/**
 * Class BacklogItemResult
 *
 * @property-read int|null $id Backlog identifier
 * @property-read int|null $groupId Identifier of the group (scrum) to which the backlog belongs
 * @property-read int|null $createdBy Created by user identifier
 * @property-read int|null $modifiedBy Modified by user identifier
 */
class BacklogItemResult extends AbstractItem
{
}
