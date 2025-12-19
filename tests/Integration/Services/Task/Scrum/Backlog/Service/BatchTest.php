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

namespace Bitrix24\SDK\Tests\Integration\Services\Task\Scrum\Backlog\Service;

use Bitrix24\SDK\Core\Exceptions\BaseException;
use Bitrix24\SDK\Core\Exceptions\TransportException;
use Bitrix24\SDK\Services\ServiceBuilder;
use Bitrix24\SDK\Services\Task\Scrum\Backlog\Service\Batch;
use Bitrix24\SDK\Tests\CustomAssertions\CustomBitrix24Assertions;
use Bitrix24\SDK\Tests\Integration\Fabric;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

/**
 * Class BatchTest
 *
 * Integration tests for Backlog batch operations
 *
 * @package Bitrix24\SDK\Tests\Integration\Services\Task\Scrum\Backlog\Service
 */
#[CoversClass(Batch::class)]
#[CoversMethod(Batch::class, 'add')]
#[CoversMethod(Batch::class, 'update')]
#[CoversMethod(Batch::class, 'delete')]
#[CoversMethod(Batch::class, 'list')]
class BatchTest extends TestCase
{
    use CustomBitrix24Assertions;

    private Batch $backlogBatchService;

    private ServiceBuilder $serviceBuilder;

    private static array $testGroupIds = [];

    private static array $createdBacklogIds = [];

    /**
     * Clean up created backlogs
     */
    private function cleanupBacklogs(): void
    {
        try {
            foreach (self::$createdBacklogIds as $key => $backlogId) {
                try {
                    $this->serviceBuilder->getTaskScope()->backlog()->delete($backlogId);
                    unset(self::$createdBacklogIds[$key]);
                } catch (\Exception) {
                    // Continue with other backlogs
                }
            }
        } catch (\Exception) {
            // Ignore cleanup errors
        }
    }

    /**
     * Create multiple test Scrum groups for batch operations
     *
     * @throws BaseException
     * @throws TransportException
     */
    private function getMultipleTestGroupIds(int $count = 3): array
    {
        $groupIds = [];
        $core = Fabric::getCore();

        // Get current user ID for SCRUM_MASTER
        $currentUser = $core->call('user.current');
        $currentUserId = (int)$currentUser->getResponseData()->getResult()['ID'];

        for ($i = 0; $i < $count; $i++) {
            $groupResult = $core->call('sonet_group.create', [
                'NAME' => 'Test Scrum Group for Backlog Batch Tests ' . uniqid('batch_backlog_', true),
                'DESCRIPTION' => 'Auto-generated test group for Backlog batch service integration tests',
                'VISIBLE' => 'Y',
                'OPENED' => 'N',
                'SCRUM_MASTER_ID' => $currentUserId, // Use current user as Scrum master
            ]);

            $groupId = (int)$groupResult->getResponseData()->getResult()[0];
            $groupIds[] = $groupId;
            self::$testGroupIds[] = $groupId; // Track for cleanup
            
            // Create backlog for each group
            try {
                // Get current user ID for createdBy field
                $core = Fabric::getCore();
                $currentUser = $core->call('user.current');
                $currentUserId = (int)$currentUser->getResponseData()->getResult()['ID'];
                
                $addResult = $this->serviceBuilder->getTaskScope()->backlog()->add($groupId, $currentUserId);
                if ($addResult->isSuccess()) {
                    self::$createdBacklogIds[] = $addResult->getId(); // Track for cleanup
                }
            } catch (BaseException $e) {
                // Might already exist, ignore
            }
        }

        return $groupIds;
    }

    /**
     * @throws BaseException
     * @throws TransportException
     */
    protected function setUp(): void
    {
        $this->serviceBuilder = Fabric::getServiceBuilder();
        $this->backlogBatchService = $this->serviceBuilder->getTaskScope()->backlog()->batch;
    }

    /**
     * Clean up after each test method
     */
    protected function tearDown(): void
    {
        // Force cleanup of any remaining backlogs after each test
        $this->cleanupBacklogs();
    }

    /**
     * Test batch add operation for backlogs
     * Note: This tests data import scenarios, as backlogs are auto-created normally
     *
     * @throws BaseException
     * @throws TransportException
     */
    public function testBatchAdd(): void
    {
        $groupIds = $this->getMultipleTestGroupIds(2);

        // Get existing backlogs first and delete them
        $core = Fabric::getCore();
        $existingBacklogIds = [];
        
        foreach ($groupIds as $groupId) {
            try {
                $backlogResult = $core->call('tasks.api.scrum.backlog.get', ['id' => $groupId]);
                if ($backlogResult->getResponseData()->getResult()) {
                    $backlogData = $backlogResult->getResponseData()->getResult();
                    $existingBacklogIds[$groupId] = $backlogData['id'];
                    // Delete existing backlog
                    $core->call('tasks.api.scrum.backlog.delete', ['id' => $backlogData['id']]);
                }
            } catch (\Exception) {
                // May not exist yet, ignore
            }
        }

        // Get current user ID for createdBy parameter
        $currentUser = $core->call('user.current');
        $currentUserId = (int)$currentUser->getResponseData()->getResult()['ID'];

        $backlogData = [
            [
                'fields' => [
                    'groupId' => $groupIds[0],
                    'createdBy' => $currentUserId,
                ]
            ],
            [
                'fields' => [
                    'groupId' => $groupIds[1], 
                    'createdBy' => $currentUserId,
                ]
            ],
        ];

        $addedBacklogs = [];
        $addedCount = 0;

        foreach ($this->backlogBatchService->add($backlogData) as $addResult) {
            $this->assertIsInt($addResult->getId());
            $this->assertGreaterThan(0, $addResult->getId());

            $addedBacklogs[] = $addResult->getId();
            self::$createdBacklogIds[] = $addResult->getId(); // Track for cleanup
            $addedCount++;
        }

        $this->assertEquals(count($backlogData), $addedCount);
        $this->assertCount(count($backlogData), $addedBacklogs);

        // Verify backlogs exist
        foreach ($groupIds as $groupId) {
            $backlogResult = $this->serviceBuilder->getTaskScope()->backlog()->get($groupId);
            $backlog = $backlogResult->backlog();
            $this->assertEquals($groupId, $backlog->groupId);
            $this->assertContains($backlog->id, $addedBacklogs);
        }
    }

    /**
     * Test batch update operation for backlogs
     *
     * @throws BaseException
     * @throws TransportException
     */
    public function testBatchUpdate(): void
    {
        $groupIds = $this->getMultipleTestGroupIds(2);

        // Get existing backlogs (auto-created) and their IDs
        $backlog1 = $this->serviceBuilder->getTaskScope()->backlog()->get($groupIds[0])->backlog();
        $backlog2 = $this->serviceBuilder->getTaskScope()->backlog()->get($groupIds[1])->backlog();

        $updateData = [
            $backlog1->id => [  // Use actual backlog ID as key
                'fields' => [
                    'modifiedBy' => 1,
                ],
            ],
            $backlog2->id => [  // Use actual backlog ID as key
                'fields' => [
                    'modifiedBy' => 1,
                ],
            ],
        ];

        $updatedCount = 0;
        foreach ($this->backlogBatchService->update($updateData) as $updateResult) {
            $updatedCount++;
        }

        $this->assertEquals(count($updateData), $updatedCount);

        // Verify backlogs still exist and have same IDs
        $updatedBacklog1 = $this->serviceBuilder->getTaskScope()->backlog()->get($groupIds[0])->backlog();
        $updatedBacklog2 = $this->serviceBuilder->getTaskScope()->backlog()->get($groupIds[1])->backlog();

        $this->assertEquals($backlog1->id, $updatedBacklog1->id);
        $this->assertEquals($backlog2->id, $updatedBacklog2->id);
        $this->assertEquals($groupIds[0], $updatedBacklog1->groupId);
        $this->assertEquals($groupIds[1], $updatedBacklog2->groupId);
    }

    /**
     * Test batch delete operation for backlogs
     * Note: Backlogs are auto-recreated, so this tests the delete operation itself
     *
     * @throws BaseException
     * @throws TransportException
     */
    public function testBatchDelete(): void
    {
        $groupIds = $this->getMultipleTestGroupIds(2);

        // Get existing backlogs and their IDs
        $backlog1 = $this->serviceBuilder->getTaskScope()->backlog()->get($groupIds[0])->backlog();
        $backlog2 = $this->serviceBuilder->getTaskScope()->backlog()->get($groupIds[1])->backlog();

        $deleteData = [$backlog1->id, $backlog2->id]; // Use simple array of IDs

        $deletedCount = 0;
        foreach ($this->backlogBatchService->delete($deleteData) as $deleteResult) {
            $deletedCount++;
        }

        $this->assertEquals(count($deleteData), $deletedCount);
    }

    /**
     * Clean up test groups after all tests
     *
     * @throws BaseException
     * @throws TransportException
     */
    public static function tearDownAfterClass(): void
    {
        try {
            $core = Fabric::getCore();

            // Clean up all created backlogs (final cleanup)
            foreach (self::$createdBacklogIds as $createdBacklogId) {
                try {
                    $core->call('tasks.api.scrum.backlog.delete', ['id' => $createdBacklogId]);
                } catch (\Exception) {
                    // Ignore individual backlog deletion errors
                }
            }

            // Clean up all test groups (this will also clean up backlogs)
            foreach (self::$testGroupIds as $groupId) {
                try {
                    $core->call('sonet_group.delete', [
                        'GROUP_ID' => $groupId,
                    ]);
                } catch (\Exception $e) {
                    // Log error but continue
                    error_log("Failed to delete test group " . $groupId . ": " . $e->getMessage());
                }
            }

            // Clear tracking arrays
            self::$testGroupIds = [];
            self::$createdBacklogIds = [];

        } catch (\Exception $exception) {
            // Log error but don't break test results
            error_log("Failed cleanup in tearDownAfterClass: " . $exception->getMessage());
        }
    }
}