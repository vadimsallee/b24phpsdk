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
     * @param int $groupId Group (scrum) identifier
     * @param int $createdBy User identifier who creates the backlog
     * @param int|null $modifiedBy User identifier who modified the backlog (optional)
     *
     * @throws BaseException
     * @throws TransportException
     */
    #[ApiEndpointMetadata(
        'tasks.api.scrum.backlog.add',
        'https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/backlog/tasks-api-scrum-backlog-add.html',
        'Adds a backlog to Scrum.'
    )]
    public function add(int $groupId, int $createdBy, ?int $modifiedBy = null): BacklogAddedResult
    {
        $fields = [
            'groupId' => $groupId,
            'createdBy' => $createdBy,
        ];

        if ($modifiedBy !== null) {
            $fields['modifiedBy'] = $modifiedBy;
        }

        return new BacklogAddedResult(
            $this->core->call('tasks.api.scrum.backlog.add', [
                'fields' => $fields
            ])
        );
    }

    /**
     * Updates a backlog in Scrum.
     *
     * @link https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/backlog/tasks-api-scrum-backlog-update.html
     *
     * @param int   $backlogId Backlog identifier (not groupId!)
     * @param array $fields    Backlog fields for update
     *
     * @throws BaseException
     * @throws TransportException
     */
    #[ApiEndpointMetadata(
        'tasks.api.scrum.backlog.update',
        'https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/backlog/tasks-api-scrum-backlog-update.html',
        'Updates a backlog in Scrum.'
    )]
    public function update(int $backlogId, array $fields): BacklogUpdatedResult
    {
        return new BacklogUpdatedResult(
            $this->core->call('tasks.api.scrum.backlog.update', [
                'id' => $backlogId, // API expects backlogId, not groupId
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
                'id' => $groupId, // API expects 'id' parameter but it's actually groupId
            ])
        );
    }

    /**
     * Deletes a backlog.
     * Note: The backlog will be automatically recreated when the planning page is opened.
     *
     * @link https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/backlog/tasks-api-scrum-backlog-delete.html
     *
     * @param int $backlogId Backlog identifier (not groupId!)
     *
     * @throws BaseException
     * @throws TransportException
     */
    #[ApiEndpointMetadata(
        'tasks.api.scrum.backlog.delete',
        'https://apidocs.bitrix24.com/api-reference/sonet-group/scrum/backlog/tasks-api-scrum-backlog-delete.html',
        'Deletes a backlog.'
    )]
    public function delete(int $backlogId): BacklogDeletedResult
    {
        return new BacklogDeletedResult(
            $this->core->call('tasks.api.scrum.backlog.delete', [
                'id' => $backlogId, // API expects backlogId, not groupId
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

    // Helper methods for working with groupId instead of backlogId

    /**
     * Updates a backlog by group ID.
     * This is a convenience method that first gets the backlog by group ID, then updates it.
     *
     * @param int   $groupId Group (scrum) identifier
     * @param array $fields  Backlog fields for update
     *
     * @throws BaseException
     * @throws TransportException
     */
    public function updateByGroupId(int $groupId, array $fields): BacklogUpdatedResult
    {
        $backlog = $this->get($groupId)->backlog();
        return $this->update($backlog->id, $fields);
    }

    /**
     * Deletes a backlog by group ID.
     * This is a convenience method that first gets the backlog by group ID, then deletes it.
     *
     * @param int $groupId Group (scrum) identifier
     *
     * @throws BaseException
     * @throws TransportException
     */
    public function deleteByGroupId(int $groupId): BacklogDeletedResult
    {
        $backlog = $this->get($groupId)->backlog();
        return $this->delete($backlog->id);
    }
}
