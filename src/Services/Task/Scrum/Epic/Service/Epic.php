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

namespace Bitrix24\SDK\Services\Task\Scrum\Epic\Service;

use Bitrix24\SDK\Attributes\ApiEndpointMetadata;
use Bitrix24\SDK\Attributes\ApiServiceMetadata;
use Bitrix24\SDK\Core\Contracts\CoreInterface;
use Bitrix24\SDK\Core\Credentials\Scope;
use Bitrix24\SDK\Core\Exceptions\BaseException;
use Bitrix24\SDK\Core\Exceptions\TransportException;
use Bitrix24\SDK\Services\AbstractService;
use Bitrix24\SDK\Services\Task\Scrum\Epic\Result\EpicAddedResult;
use Bitrix24\SDK\Services\Task\Scrum\Epic\Result\EpicDeletedResult;
use Bitrix24\SDK\Services\Task\Scrum\Epic\Result\EpicFieldsResult;
use Bitrix24\SDK\Services\Task\Scrum\Epic\Result\EpicResult;
use Bitrix24\SDK\Services\Task\Scrum\Epic\Result\EpicsResult;
use Bitrix24\SDK\Services\Task\Scrum\Epic\Result\EpicUpdatedResult;
use Psr\Log\LoggerInterface;

#[ApiServiceMetadata(new Scope(['task']))]
class Epic extends AbstractService
{
    /**
     * Epic constructor.
     */
    public function __construct(public Batch $batch, CoreInterface $core, LoggerInterface $logger)
    {
        parent::__construct($core, $logger);
    }

    /**
     * Adds an epic to Scrum.
     *
     * @link https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/epic/tasks-api-scrum-epic-add.html
     *
     * @param array $fields Epic fields for creation
     *
     * @throws BaseException
     * @throws TransportException
     */
    #[ApiEndpointMetadata(
        'tasks.api.scrum.epic.add',
        'https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/epic/tasks-api-scrum-epic-add.html',
        'Adds an epic to Scrum.'
    )]
    public function add(array $fields): EpicAddedResult
    {
        return new EpicAddedResult(
            $this->core->call('tasks.api.scrum.epic.add', ['fields' => $fields])
        );
    }

    /**
     * Updates an epic in Scrum.
     *
     * @link https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/epic/tasks-api-scrum-epic-update.html
     *
     * @param int   $epicId Epic identifier
     * @param array $fields Epic fields for update
     *
     * @throws BaseException
     * @throws TransportException
     */
    #[ApiEndpointMetadata(
        'tasks.api.scrum.epic.update',
        'https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/epic/tasks-api-scrum-epic-update.html',
        'Updates an epic in Scrum.'
    )]
    public function update(int $epicId, array $fields): EpicUpdatedResult
    {
        return new EpicUpdatedResult(
            $this->core->call('tasks.api.scrum.epic.update', [
                'id' => $epicId,
                'fields' => $fields,
            ])
        );
    }

    /**
     * Retrieves field values of an epic by its id.
     *
     * @link https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/epic/tasks-api-scrum-epic-get.html
     *
     * @param int $epicId Epic identifier
     *
     * @throws BaseException
     * @throws TransportException
     */
    #[ApiEndpointMetadata(
        'tasks.api.scrum.epic.get',
        'https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/epic/tasks-api-scrum-epic-get.html',
        'Retrieves field values of an epic by its id.'
    )]
    public function get(int $epicId): EpicResult
    {
        return new EpicResult(
            $this->core->call('tasks.api.scrum.epic.get', [
                'id' => $epicId,
            ])
        );
    }

    /**
     * Retrieves a list of epics.
     *
     * @link https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/epic/tasks-api-scrum-epic-list.html
     *
     * @param array $order  Sort order
     * @param array $filter Filter criteria
     * @param array $select Fields to select
     *
     * @throws BaseException
     * @throws TransportException
     */
    #[ApiEndpointMetadata(
        'tasks.api.scrum.epic.list',
        'https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/epic/tasks-api-scrum-epic-list.html',
        'Retrieves a list of epics.'
    )]
    public function list(array $order = [], array $filter = [], array $select = []): EpicsResult
    {
        return new EpicsResult(
            $this->core->call('tasks.api.scrum.epic.list', [
                'order' => $order,
                'filter' => $filter,
                'select' => $select,
            ])
        );
    }

    /**
     * Deletes an epic.
     *
     * @link https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/epic/tasks-api-scrum-epic-delete.html
     *
     * @param int $epicId Epic identifier
     *
     * @throws BaseException
     * @throws TransportException
     */
    #[ApiEndpointMetadata(
        'tasks.api.scrum.epic.delete',
        'https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/epic/tasks-api-scrum-epic-delete.html',
        'Deletes an epic.'
    )]
    public function delete(int $epicId): EpicDeletedResult
    {
        return new EpicDeletedResult(
            $this->core->call('tasks.api.scrum.epic.delete', [
                'id' => $epicId,
            ])
        );
    }

    /**
     * Retrieves available fields of an epic.
     *
     * @link https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/epic/tasks-api-scrum-epic-get-fields.html
     *
     * @throws BaseException
     * @throws TransportException
     */
    #[ApiEndpointMetadata(
        'tasks.api.scrum.epic.getFields',
        'https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/epic/tasks-api-scrum-epic-get-fields.html',
        'Retrieves available fields of an epic.'
    )]
    public function getFields(): EpicFieldsResult
    {
        return new EpicFieldsResult(
            $this->core->call('tasks.api.scrum.epic.getFields')
        );
    }
}
