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

namespace Bitrix24\SDK\Services\Task\Scrum\Epic;

use Bitrix24\SDK\Core\Exceptions\BaseException;
use Bitrix24\SDK\Core\Exceptions\InvalidArgumentException;
use Bitrix24\SDK\Core\Response\DTO\ResponseData;
use Generator;

/**
 * Class Batch
 *
 * @package Bitrix24\SDK\Services\Task\Scrum\Epic
 */
class Batch extends \Bitrix24\SDK\Core\Batch
{
    /**
     * Delete entity items with batch call for epics
     *
     * @param string $apiMethod
     * @param array $entityItemId
     * @param array|null $additionalParameters
     *
     * @return Generator<int, ResponseData>|ResponseData[]
     * @throws BaseException
     */
    public function deleteEntityItems(
        string $apiMethod,
        array $entityItemId,
        ?array $additionalParameters = null
    ): Generator {
        $this->logger->debug(
            'deleteEntityItems.start',
            [
                'apiMethod' => $apiMethod,
                'entityItems' => $entityItemId,
                'additionalParameters' => $additionalParameters,
            ]
        );

        try {
            $this->clearCommands();
            foreach ($entityItemId as $cnt => $itemId) {
                if (!is_int($itemId)) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'invalid type «%s» of epic id «%s» at position %s, epic id must be integer type',
                            gettype($itemId),
                            $itemId,
                            $cnt
                        )
                    );
                }

                // For epics API, use 'id' parameter instead of 'ID'
                $this->registerCommand($apiMethod, ['id' => $itemId]);
            }

            foreach ($this->getTraversable(true) as $cnt => $deletedItemResult) {
                $this->logger->debug('deleteEntityItems', ['result' => $deletedItemResult->getResult()]);
                yield $cnt => $deletedItemResult;
            }
        } catch (InvalidArgumentException $exception) {
            $errorMessage = sprintf('batch delete epics: %s', $exception->getMessage());
            $this->logger->error(
                $errorMessage,
                [
                    'trace' => $exception->getTrace(),
                ]
            );
            throw $exception;
        } catch (\Throwable $exception) {
            $errorMessage = sprintf('batch delete epics: %s', $exception->getMessage());
            $this->logger->error(
                $errorMessage,
                [
                    'trace' => $exception->getTrace(),
                ]
            );

            throw new BaseException($errorMessage, $exception->getCode(), $exception);
        }

        $this->logger->debug('deleteEntityItems.finish');
    }
}