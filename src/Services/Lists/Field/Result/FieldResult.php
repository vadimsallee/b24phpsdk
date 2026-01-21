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

use Bitrix24\SDK\Core\Exceptions\BaseException;
use Bitrix24\SDK\Core\Result\AbstractResult;

/**
 * Class FieldResult
 *
 * @package Bitrix24\SDK\Services\Lists\Field\Result
 */
class FieldResult extends AbstractResult
{
    /**
     * Get field data
     *
     * @return FieldItemResult
     * @throws BaseException
     */
    public function field(): FieldItemResult
    {
        echo "\n\n Field data \n";
        print_r($this->getCoreResponse()->getResponseData()->getResult());
        echo "\n\n";
        
        $result = $this->getCoreResponse()->getResponseData()->getResult();
        if (is_array($result)) {
            $result = current($result);
        }

        return new FieldItemResult($result);
    }
}
