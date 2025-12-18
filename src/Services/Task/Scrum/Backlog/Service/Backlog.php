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

namespace Bitrix24\SDK\Services\Task\Scrum\Backlog\Service;

use Bitrix24\SDK\Attributes\ApiEndpointMetadata;
use Bitrix24\SDK\Attributes\ApiServiceMetadata;
use Bitrix24\SDK\Core\Contracts\CoreInterface;
use Bitrix24\SDK\Core\Credentials\Scope;
use Bitrix24\SDK\Core\Exceptions\BaseException;
use Bitrix24\SDK\Core\Exceptions\TransportException;
use Bitrix24\SDK\Services\AbstractService;
use Bitrix24\SDK\Services\Task\Scrum\Backlog\Result\BacklogAddedResult;
use Bitrix24\SDK\Services\Task\Scrum\Backlog\Result\BacklogDeletedResult;
use Bitrix24\SDK\Services\Task\Scrum\Backlog\Result\BacklogFieldsResult;
use Bitrix24\SDK\Services\Task\Scrum\Backlog\Result\BacklogResult;
use Bitrix24\SDK\Services\Task\Scrum\Backlog\Result\BacklogUpdatedResult;
use Psr\Log\LoggerInterface;

#[ApiServiceMetadata(new Scope(['task']))]
class Backlog extends AbstractService
{
    /**
     * Backlog constructor.
     */
    public function __construct(public Batch $batch, CoreInterface $core, LoggerInterface $logger)
    {
        parent::__construct($core, $logger);
    }

    /**
     * Adds a backlog to Scrum.
     * Note: Bitrix24 automatically creates a backlog when creating Scrum.
     * This method is primarily for data import from other systems.
     *
     * @link https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/backlog/tasks-api-scrum-backlog-add.html
     *
     * @param int   $groupId Group (scrum) identifier
     * @param array $fields  Backlog fields for creation (optional, usually empty)
     *
     * @throws BaseException
     * @throws TransportException
     */
    #[ApiEndpointMetadata(
        'tasks.api.scrum.backlog.add',
        'https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/backlog/tasks-api-scrum-backlog-add.html',
        'Adds a backlog to Scrum.'
    )]
    public function add(int $groupId, array $fields = []): BacklogAddedResult
    {
        $params = ['groupId' => $groupId];
        if ($fields !== []) {
            $params['fields'] = $fields;
        }

        return new BacklogAddedResult(
            $this->core->call('tasks.api.scrum.backlog.add', $params)
        );
    }

    /**
     * Updates a backlog in Scrum.
     *
     * @link https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/backlog/tasks-api-scrum-backlog-update.html
     *
     * @param int   $groupId Group (scrum) identifier
     * @param array $fields  Backlog fields for update
     *
     * @throws BaseException
     * @throws TransportException
     */
    #[ApiEndpointMetadata(
        'tasks.api.scrum.backlog.update',
        'https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/backlog/tasks-api-scrum-backlog-update.html',
        'Updates a backlog in Scrum.'
    )]
    public function update(int $groupId, array $fields): BacklogUpdatedResult
    {
        return new BacklogUpdatedResult(
            $this->core->call('tasks.api.scrum.backlog.update', [
                'groupId' => $groupId,
                'fields' => $fields,
            ])
        );
    }

    /**
     * Retrieves field values of a backlog by group (scrum) id.
     *
     * @link https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/backlog/tasks-api-scrum-backlog-get.html
     *
     * @param int $groupId Group (scrum) identifier
     *
     * @throws BaseException
     * @throws TransportException
     */
    #[ApiEndpointMetadata(
        'tasks.api.scrum.backlog.get',
        'https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/backlog/tasks-api-scrum-backlog-get.html',
        'Retrieves field values of a backlog by group (scrum) id.'
    )]
    public function get(int $groupId): BacklogResult
    {
        return new BacklogResult(
            $this->core->call('tasks.api.scrum.backlog.get', [
                'groupId' => $groupId,
            ])
        );
    }

    /**
     * Deletes a backlog.
     * Note: The backlog will be automatically recreated when the planning page is opened.
     *
     * @link https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/backlog/tasks-api-scrum-backlog-delete.html
     *
     * @param int $groupId Group (scrum) identifier
     *
     * @throws BaseException
     * @throws TransportException
     */
    #[ApiEndpointMetadata(
        'tasks.api.scrum.backlog.delete',
        'https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/backlog/tasks-api-scrum-backlog-delete.html',
        'Deletes a backlog.'
    )]
    public function delete(int $groupId): BacklogDeletedResult
    {
        return new BacklogDeletedResult(
            $this->core->call('tasks.api.scrum.backlog.delete', [
                'groupId' => $groupId,
            ])
        );
    }

    /**
     * Retrieves available fields of a backlog.
     *
     * @link https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/backlog/tasks-api-scrum-backlog-get-fields.html
     *
     * @throws BaseException
     * @throws TransportException
     */
    #[ApiEndpointMetadata(
        'tasks.api.scrum.backlog.getFields',
        'https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/backlog/tasks-api-scrum-backlog-get-fields.html',
        'Retrieves available fields of a backlog.'
    )]
    public function getFields(): BacklogFieldsResult
    {
        return new BacklogFieldsResult(
            $this->core->call('tasks.api.scrum.backlog.getFields')
        );
    }
}
