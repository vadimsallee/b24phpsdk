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

use Bitrix24\SDK\Core\Result\AbstractResult;

/**
 * Class AddedFieldResult
 *
 * @package Bitrix24\SDK\Services\Lists\Field\Result
 */
class AddedFieldResult extends AbstractResult
{
    /**
     * Get created field identifier
     *
     * @return string
     */
    public function getId(): string
    {
        $result = $this->getCoreResponse()->getResponseData()->getResult();
        
        echo "\n\n AddedFieldResult \n";
        print_r($result);
        echo "\n\n";
        
        return (string) $result[0];
    }
}
