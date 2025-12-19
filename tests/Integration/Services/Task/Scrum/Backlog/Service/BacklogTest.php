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
use Bitrix24\SDK\Core;
use Bitrix24\SDK\Services\ServiceBuilder;
use Bitrix24\SDK\Services\Task\Scrum\Backlog\Result\BacklogItemResult;
use Bitrix24\SDK\Services\Task\Scrum\Backlog\Service\Backlog;
use Bitrix24\SDK\Tests\CustomAssertions\CustomBitrix24Assertions;
use Bitrix24\SDK\Tests\Integration\Fabric;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

/**
 * Class BacklogTest
 *
 * Integration tests for Backlog service
 *
 * @package Bitrix24\SDK\Tests\Integration\Services\Task\Scrum\Backlog\Service
 */
#[CoversClass(Backlog::class)]
#[CoversMethod(Backlog::class, 'add')]
#[CoversMethod(Backlog::class, 'update')]
#[CoversMethod(Backlog::class, 'get')]
#[CoversMethod(Backlog::class, 'delete')]
#[CoversMethod(Backlog::class, 'getFields')]
class BacklogTest extends TestCase
{
    use CustomBitrix24Assertions;

    private Backlog $backlogService;

    private ServiceBuilder $serviceBuilder;

    private static ?int $testGroupId = null;

    private static array $createdBacklogIds = [];

    /**
     * Clean up created backlogs after each test
     */
    private function cleanupBacklogs(): void
    {
        try {
            foreach (self::$createdBacklogIds as $key => $backlogId) {
                try {
                    $this->backlogService->delete($backlogId);
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
                'NAME' => 'Test Scrum Group for Backlog Tests ' . uniqid('backlog_', true),
                'DESCRIPTION' => 'Auto-generated test group for Backlog service integration tests',
                'VISIBLE' => 'Y',
                'OPENED' => 'N',
                'SCRUM_MASTER_ID' => $currentUserId, // Use current user as Scrum master
            ]);

            self::$testGroupId = (int)$groupResult->getResponseData()->getResult()[0];
        }

        return self::$testGroupId;
    }

    /**
     * Create backlog for the test group
     *
     * @param int $groupId Group ID to create backlog for
     * @return int Created backlog ID
     * @throws BaseException
     * @throws TransportException
     */
    private function createTestBacklog(int $groupId): int
    {
        // Get current user ID for createdBy parameter
        $core = Fabric::getCore();
        $currentUser = $core->call('user.current');
        $currentUserId = (int)$currentUser->getResponseData()->getResult()['ID'];
        
        $addResult = $this->backlogService->add($groupId, $currentUserId);
        if (!$addResult->isSuccess()) {
            throw new BaseException('Failed to create test backlog');
        }
        
        $backlogId = $addResult->getId();
        self::$createdBacklogIds[] = $backlogId; // Track for cleanup
        
        return $backlogId;
    }

    /**
     * @throws BaseException
     * @throws TransportException
     */
    protected function setUp(): void
    {
        $this->serviceBuilder = Fabric::getServiceBuilder();
        $this->backlogService = $this->serviceBuilder->getTaskScope()->backlog();
    }

    /**
     * Clean up after each test method
     */
    protected function tearDown(): void
    {
        // Force cleanup of any remaining backlogs after each test
        $this->cleanupBacklogs();
    }

    public function testAllSystemFieldsAnnotated(): void
    {
        $fields = $this->backlogService->getFields()->getFieldsDescription();
        $propListFromApi = (new Core\Fields\FieldsFilter())->filterSystemFields(array_keys($fields));
        $this->assertBitrix24AllResultItemFieldsAnnotated($propListFromApi, BacklogItemResult::class);
    }

    public function testAllSystemFieldsHasValidTypeAnnotation(): void
    {
        $allFields = $this->backlogService->getFields()->getFieldsDescription();
        $systemFieldsCodes = (new Core\Fields\FieldsFilter())->filterSystemFields(array_keys($allFields));
        $systemFields = array_filter($allFields, static fn($code): bool => in_array($code, $systemFieldsCodes, true), ARRAY_FILTER_USE_KEY);
        $this->assertBitrix24AllResultItemFieldsHasValidTypeAnnotation(
            $systemFields,
            BacklogItemResult::class
        );
    }

    /**
     * Test Backlog service fields method
     *
     * @throws BaseException
     * @throws TransportException
     */
    public function testFields(): void
    {
        $backlogFieldsResult = $this->backlogService->getFields();

        // Get the raw result to examine structure
        $this->assertNotNull($backlogFieldsResult);

        // Check if we can access field descriptions
        $fields = $backlogFieldsResult->getFieldsDescription();
        $this->assertIsArray($fields);
        $this->assertNotEmpty($fields);

        // Check expected Backlog fields based on API documentation
        $this->assertArrayHasKey('groupId', $fields);
        $this->assertArrayHasKey('createdBy', $fields);
        $this->assertArrayHasKey('modifiedBy', $fields);

        // Verify field types
        $this->assertEquals('integer', $fields['groupId']['type']);
        $this->assertEquals('integer', $fields['createdBy']['type']);
        $this->assertEquals('integer', $fields['modifiedBy']['type']);
    }

    /**
     * Test Backlog CRUD operations
     *
     * Note: Backlog workflow:
     * - Create Scrum group with SCRUM_MASTER_ID
     * - Explicitly create backlog for the group using add() 
     * - Then can get, update, delete the backlog
     *
     * @throws BaseException
     * @throws TransportException
     */
    public function testCrudOperations(): void
    {
        $groupId = $this->getTestGroupId();

        // Explicitly create backlog for this test
        $backlogId = $this->createTestBacklog($groupId);

        // Test get backlog
        $backlogResult = $this->backlogService->get($groupId);
        $backlog = $backlogResult->backlog();

        $this->assertInstanceOf(BacklogItemResult::class, $backlog);
        $this->assertIsInt($backlog->id);
        $this->assertEquals($groupId, $backlog->groupId);
        $this->assertIsInt($backlog->createdBy);

        $originalBacklogId = $backlog->id;

        // Test update backlog using helper method for groupId
        $backlogUpdatedResult = $this->backlogService->updateByGroupId($groupId, [
            // Backlog has minimal update fields, mostly internal
            // We're just testing that update doesn't fail
        ]);

        $this->assertTrue($backlogUpdatedResult->isSuccess());

        // Verify backlog still exists and has same ID
        $getUpdatedResult = $this->backlogService->get($groupId);
        $updatedBacklog = $getUpdatedResult->backlog();
        $this->assertEquals($originalBacklogId, $updatedBacklog->id);
        $this->assertEquals($groupId, $updatedBacklog->groupId);

        // Test delete backlog (special behavior: it gets recreated automatically)
        $backlogDeletedResult = $this->backlogService->deleteByGroupId($groupId);
        $this->assertTrue($backlogDeletedResult->isSuccess());

        // Test add backlog again (this should recreate it)
        // Get current user ID for createdBy field
        $core = Fabric::getCore();
        $currentUser = $core->call('user.current');
        $currentUserId = (int)$currentUser->getResponseData()->getResult()['ID'];
        
        $backlogAddedResult = $this->backlogService->add($groupId, $currentUserId);
        $this->assertTrue($backlogAddedResult->isSuccess());
        self::$createdBacklogIds[] = $backlogAddedResult->getId(); // Track for cleanup
        
        // Verify new backlog exists
        $newBacklogResult = $this->backlogService->get($groupId);
        $newBacklog = $newBacklogResult->backlog();
        $this->assertEquals($groupId, $newBacklog->groupId);
    }

    /**
     * Test backlog creation and access behavior
     *
     * @throws BaseException
     * @throws TransportException
     */
    public function testBacklogCreationAndAccess(): void
    {
        $groupId = $this->getTestGroupId();

        // Explicitly create backlog
        $backlogId = $this->createTestBacklog($groupId);

        // Get backlog - should exist after explicit creation
        $backlogResult = $this->backlogService->get($groupId);
        $backlog = $backlogResult->backlog();

        $this->assertInstanceOf(BacklogItemResult::class, $backlog);
        $this->assertEquals($groupId, $backlog->groupId);
        $this->assertIsInt($backlog->id);
        $this->assertGreaterThan(0, $backlog->id);

        // Verify the backlog belongs to the correct group
        $this->assertEquals($groupId, $backlog->groupId);
    }

    /**
     * Test backlog uniqueness per group
     *
     * @throws BaseException
     * @throws TransportException
     */
    public function testBacklogUniquenessPerGroup(): void
    {
        $groupId = $this->getTestGroupId();

        // Explicitly create backlog
        $backlogId = $this->createTestBacklog($groupId);

        // Get backlog multiple times - should return same backlog
        $backlog1 = $this->backlogService->get($groupId)->backlog();
        $backlog2 = $this->backlogService->get($groupId)->backlog();

        $this->assertEquals($backlog1->id, $backlog2->id);
        $this->assertEquals($backlog1->groupId, $backlog2->groupId);
        $this->assertEquals($groupId, $backlog1->groupId);
        $this->assertEquals($groupId, $backlog2->groupId);
    }

    /**
     * Test error handling for non-existent group
     *
     * @throws BaseException
     * @throws TransportException
     */
    public function testErrorHandlingForNonExistentGroup(): void
    {
        $nonExistentGroupId = 999999; // Very unlikely to exist

        $this->expectException(BaseException::class);
        $this->backlogService->get($nonExistentGroupId);
    }

    /**
     * Test error handling for invalid group ID
     *
     * @throws BaseException
     * @throws TransportException
     */
    public function testErrorHandlingForInvalidGroupId(): void
    {
        $invalidGroupId = -1;

        $this->expectException(BaseException::class);
        $this->backlogService->get($invalidGroupId);
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

            // Clean up all created backlogs (final cleanup)
            foreach (self::$createdBacklogIds as $createdBacklogId) {
                try {
                    $core->call('tasks.api.scrum.backlog.delete', ['id' => $createdBacklogId]);
                } catch (\Exception) {
                    // Ignore individual backlog deletion errors
                }
            }

            // Clean up test group (this will also clean up any remaining backlogs)
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

            // Clear backlog tracking
            self::$createdBacklogIds = [];

        } catch (\Exception $exception) {
            // Log error but don't break test results
            error_log("Failed cleanup in tearDownAfterClass: " . $exception->getMessage());
        }
    }
}