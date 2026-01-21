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

namespace Bitrix24\SDK\Services\Lists;

use Bitrix24\SDK\Attributes\ApiServiceBuilderMetadata;
use Bitrix24\SDK\Core\Credentials\Scope;
use Bitrix24\SDK\Services\AbstractServiceBuilder;
use Bitrix24\SDK\Services\Lists\Lists\Service\Batch;
use Bitrix24\SDK\Services\Lists\Lists\Service\Lists;
use Bitrix24\SDK\Services\Lists\Field\Service\Field;
use Bitrix24\SDK\Services\Lists\Field\Service\Batch as FieldBatch;

#[ApiServiceBuilderMetadata(new Scope(['lists']))]
class ListsServiceBuilder extends AbstractServiceBuilder
{
    /**
     * Lists service for managing universal lists
     */
    public function lists(): Lists
    {
        if (!isset($this->serviceCache[__METHOD__])) {
            $this->serviceCache[__METHOD__] = new Lists(
                new Batch($this->batch, $this->log),
                $this->core,
                $this->log
            );
        }

        return $this->serviceCache[__METHOD__];
    }

    /**
     * Field service for managing universal list fields
     */
    public function field(): Field
    {
        if (!isset($this->serviceCache[__METHOD__])) {
            $this->serviceCache[__METHOD__] = new Field(
                new FieldBatch($this->batch, $this->log),
                $this->core,
                $this->log
            );
        }

        return $this->serviceCache[__METHOD__];
    }
}
