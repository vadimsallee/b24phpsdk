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

namespace Bitrix24\SDK\Tests\Integration\Services\Task\Scrum\Epic\Service;

use Bitrix24\SDK\Core\Exceptions\BaseException;
use Bitrix24\SDK\Core\Exceptions\TransportException;
use Bitrix24\SDK\Services\ServiceBuilder;
use Bitrix24\SDK\Services\Task\Scrum\Epic\Service\Batch;
use Bitrix24\SDK\Tests\CustomAssertions\CustomBitrix24Assertions;
use Bitrix24\SDK\Tests\Integration\Fabric;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

/**
 * Class BatchTest
 *
 * Integration tests for Epic batch operations
 *
 * @package Bitrix24\SDK\Tests\Integration\Services\Task\Scrum\Epic\Service
 */
#[CoversClass(Batch::class)]
#[CoversMethod(Batch::class, 'add')]
#[CoversMethod(Batch::class, 'update')]
#[CoversMethod(Batch::class, 'delete')]
#[CoversMethod(Batch::class, 'list')]
class BatchTest extends TestCase
{
    use CustomBitrix24Assertions;

    private Batch $epicBatchService;

    private ServiceBuilder $serviceBuilder;

    private static ?int $testGroupId = null;

    private static array $createdEpicIds = [];

    /**
     * Get or create a test Scrum group
     *
     * @throws BaseException
     * @throws TransportException
     */
    private function getTestGroupId(): int
    {
        if (self::$testGroupId === null) {
            // Create a test group with SCRUM_MASTER_ID to make it a Scrum
            $core = Fabric::getCore();

            // Get current user ID for SCRUM_MASTER
            $currentUser = $core->call('user.current');
            $currentUserId = (int)$currentUser->getResponseData()->getResult()['ID'];

            $groupResult = $core->call('sonet_group.create', [
                'NAME' => 'Test Scrum Group for Epic Batch Tests ' . uniqid('batch_', true),
                'DESCRIPTION' => 'Auto-generated test group for Epic batch service integration tests',
                'VISIBLE' => 'Y',
                'OPENED' => 'N',
                'SCRUM_MASTER_ID' => $currentUserId, // Use current user as Scrum master
            ]);

            self::$testGroupId = (int)$groupResult->getResponseData()->getResult()[0];
        }

        return self::$testGroupId;
    }

    /**
     * Clean up all tracked epics
     */
    private function cleanupEpics(): void
    {
        try {
            $core = Fabric::getCore();

            // Clean up all tracked epics
            foreach (self::$createdEpicIds as $key => $epicId) {
                try {
                    $core->call('tasks.api.scrum.epic.delete', ['id' => $epicId]);
                    unset(self::$createdEpicIds[$key]); // Remove from tracking
                } catch (\Exception) {
                    // Continue with other epics
                }
            }
        } catch (\Exception) {
            // Ignore cleanup errors
        }
    }

    /**
     * @throws BaseException
     * @throws TransportException
     */
    protected function setUp(): void
    {
        $this->serviceBuilder = Fabric::getServiceBuilder();
        $this->epicBatchService = $this->serviceBuilder->getTaskScope()->epic()->batch;
    }

    /**
     * Clean up after each test method
     */
    protected function tearDown(): void
    {
        // Force cleanup of any remaining epics after each test
        $this->cleanupEpics();
    }

    /**
     * @throws BaseException
     * @throws TransportException
     */
    public function testBatchAdd(): void
    {
        $groupId = $this->getTestGroupId();

        $epicsData = [
            ['fields' => [
                'name' => 'Batch Epic 1',
                'description' => 'First epic added via batch',
                'groupId' => $groupId,
                'color' => '#FF0000',
            ]],
            ['fields' => [
                'name' => 'Batch Epic 2',
                'description' => 'Second epic added via batch',
                'groupId' => $groupId,
                'color' => '#00FF00',
            ]],
            ['fields' => [
                'name' => 'Batch Epic 3',
                'description' => 'Third epic added via batch',
                'groupId' => $groupId,
                'color' => '#0000FF',
            ]],
        ];

        $addedEpics = [];
        $addedCount = 0;

        foreach ($this->epicBatchService->add($epicsData) as $addResult) {
            $this->assertIsInt($addResult->getId());
            $this->assertGreaterThan(0, $addResult->getId());

            $addedEpics[] = $addResult->getId();
            self::$createdEpicIds[] = $addResult->getId(); // Track for cleanup
            $addedCount++;
        }

        $this->assertEquals(count($epicsData), $addedCount);
        $this->assertCount(count($epicsData), $addedEpics);

        // Cleanup
        foreach ($addedEpics as $addedEpic) {
            $this->serviceBuilder->getTaskScope()->epic()->delete($addedEpic);
        }
    }

    /**
     * @throws BaseException
     * @throws TransportException
     */
    public function testBatchUpdate(): void
    {
        $groupId = $this->getTestGroupId();

        // First create some epics
        $epic1Id = $this->serviceBuilder->getTaskScope()->epic()->add([
            'name' => 'Epic for Update 1',
            'description' => 'Will be updated',
            'groupId' => $groupId,
            'color' => '#FF0000',
        ])->getId();

        $epic2Id = $this->serviceBuilder->getTaskScope()->epic()->add([
            'name' => 'Epic for Update 2',
            'description' => 'Will be updated',
            'groupId' => $groupId,
            'color' => '#00FF00',
        ])->getId();

        $updateData = [
            $epic1Id => [
                'fields' => [
                    'name' => 'Updated Batch Epic 1',
                    'description' => 'Updated via batch operation',
                    'color' => '#FFFF00',
                ],
            ],
            $epic2Id => [
                'fields' => [
                    'name' => 'Updated Batch Epic 2',
                    'description' => 'Updated via batch operation',
                    'color' => '#FF00FF',
                ],
            ],
        ];

        $updatedCount = 0;
        foreach ($this->epicBatchService->update($updateData) as $updateResult) {
            $this->assertTrue($updateResult->isSuccess());
            $updatedCount++;
        }

        $this->assertEquals(count($updateData), $updatedCount);

        // Verify updates
        $epicItemResult = $this->serviceBuilder->getTaskScope()->epic()->get($epic1Id)->epic();
        $this->assertEquals('Updated Batch Epic 1', $epicItemResult->name);
        $this->assertEquals('Updated via batch operation', $epicItemResult->description);
        $this->assertEquals('#FFFF00', $epicItemResult->color);

        $epic2 = $this->serviceBuilder->getTaskScope()->epic()->get($epic2Id)->epic();
        $this->assertEquals('Updated Batch Epic 2', $epic2->name);
        $this->assertEquals('Updated via batch operation', $epic2->description);
        $this->assertEquals('#FF00FF', $epic2->color);

        // Cleanup
        $this->serviceBuilder->getTaskScope()->epic()->delete($epic1Id);
        $this->serviceBuilder->getTaskScope()->epic()->delete($epic2Id);
    }

    /**
     * @throws BaseException
     * @throws TransportException
     */
    public function testBatchDelete(): void
    {
        $groupId = $this->getTestGroupId();

        // Create epics to delete
        $epic1Id = $this->serviceBuilder->getTaskScope()->epic()->add([
            'name' => 'Epic for Delete 1',
            'description' => 'Will be deleted',
            'groupId' => $groupId,
        ])->getId();

        $epic2Id = $this->serviceBuilder->getTaskScope()->epic()->add([
            'name' => 'Epic for Delete 2',
            'description' => 'Will be deleted',
            'groupId' => $groupId,
        ])->getId();

        $deleteData = [$epic1Id, $epic2Id];

        $deletedCount = 0;
        foreach ($this->epicBatchService->delete($deleteData) as $deleteResult) {
            $this->assertTrue($deleteResult->isSuccess());
            $deletedCount++;
        }

        $this->assertEquals(count($deleteData), $deletedCount);

        // Verify deletion - should throw exceptions
        try {
            $this->serviceBuilder->getTaskScope()->epic()->get($epic1Id);
            $this->fail('Expected exception when getting deleted epic 1');
        } catch (BaseException) {
            $this->assertTrue(true);
        }

        try {
            $this->serviceBuilder->getTaskScope()->epic()->get($epic2Id);
            $this->fail('Expected exception when getting deleted epic 2');
        } catch (BaseException) {
            $this->assertTrue(true);
        }
    }

    /**
     * @throws BaseException
     * @throws TransportException
     */
    public function testBatchList(): void
    {
        $groupId = $this->getTestGroupId();

        // Create test epics
        $epic1Id = $this->serviceBuilder->getTaskScope()->epic()->add([
            'name' => 'Batch List Epic 1',
            'description' => 'For batch list testing',
            'groupId' => $groupId,
            'color' => '#123456',
        ])->getId();

        $epic2Id = $this->serviceBuilder->getTaskScope()->epic()->add([
            'name' => 'Batch List Epic 2',
            'description' => 'For batch list testing',
            'groupId' => $groupId,
            'color' => '#654321',
        ])->getId();

        $foundEpics = [];
        foreach ($this->epicBatchService->list([], ['GROUP_ID' => $groupId]) as $epic) {
            $this->assertNotNull($epic->id);
            $this->assertNotNull($epic->name);
            $this->assertEquals($groupId, $epic->groupId);

            $foundEpics[] = $epic->id;
        }

        $this->assertContains($epic1Id, $foundEpics);
        $this->assertContains($epic2Id, $foundEpics);

        // Cleanup
        $this->serviceBuilder->getTaskScope()->epic()->delete($epic1Id);
        $this->serviceBuilder->getTaskScope()->epic()->delete($epic2Id);
    }

    /**
     * Clean up test group after all tests
     *
     * @throws BaseException
     * @throws TransportException
     */
    public static function tearDownAfterClass(): void
    {
        try {
            $core = Fabric::getCore();

            // Clean up all created epics (final cleanup)
            foreach (self::$createdEpicIds as $createdEpicId) {
                try {
                    $core->call('tasks.api.scrum.epic.delete', ['id' => $createdEpicId]);
                } catch (\Exception) {
                    // Ignore individual epic deletion errors
                }
            }

            // Clean up test group
            if (self::$testGroupId !== null) {
                try {
                    $core->call('sonet_group.delete', [
                        'GROUP_ID' => self::$testGroupId,
                    ]);
                    self::$testGroupId = null; // Reset for next run
                } catch (\Exception $e) {
                    // Log error but continue
                    error_log("Failed to delete test group " . self::$testGroupId . ": " . $e->getMessage());
                }
            }

            // Clear epic tracking
            self::$createdEpicIds = [];

        } catch (\Exception $exception) {
            // Log error but don't break test results
            error_log("Failed cleanup in tearDownAfterClass: " . $exception->getMessage());
        }
    }
}
