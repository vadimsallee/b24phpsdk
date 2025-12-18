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
use Bitrix24\SDK\Core;
use Bitrix24\SDK\Services\ServiceBuilder;
use Bitrix24\SDK\Services\Task\Scrum\Epic\Result\EpicItemResult;
use Bitrix24\SDK\Services\Task\Scrum\Epic\Service\Epic;
use Bitrix24\SDK\Tests\CustomAssertions\CustomBitrix24Assertions;
use Bitrix24\SDK\Tests\Integration\Fabric;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

/**
 * Class EpicTest
 *
 * Integration tests for Epic service
 *
 * @package Bitrix24\SDK\Tests\Integration\Services\Task\Scrum\Epic\Service
 */
#[CoversClass(Epic::class)]
#[CoversMethod(Epic::class, 'add')]
#[CoversMethod(Epic::class, 'update')]
#[CoversMethod(Epic::class, 'get')]
#[CoversMethod(Epic::class, 'list')]
#[CoversMethod(Epic::class, 'delete')]
#[CoversMethod(Epic::class, 'getFields')]
class EpicTest extends TestCase
{
    use CustomBitrix24Assertions;

    private Epic $epicService;

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
                'NAME' => 'Test Scrum Group for Epic Tests ' . uniqid('epic_', true),
                'DESCRIPTION' => 'Auto-generated test group for Epic service integration tests',
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
        $this->epicService = $this->serviceBuilder->getTaskScope()->epic();
    }

    public function testAllSystemFieldsAnnotated(): void
    {
        $fields = $this->epicService->getFields()->getFieldsDescription();
        $propListFromApi = (new Core\Fields\FieldsFilter())->filterSystemFields(array_keys($fields));
        $this->assertBitrix24AllResultItemFieldsAnnotated($propListFromApi, EpicItemResult::class);
    }

    public function testAllSystemFieldsHasValidTypeAnnotation(): void
    {
        $allFields = $this->epicService->getFields()->getFieldsDescription();
        $systemFieldsCodes = (new Core\Fields\FieldsFilter())->filterSystemFields(array_keys($allFields));
        $systemFields = array_filter($allFields, static fn($code): bool => in_array($code, $systemFieldsCodes, true), ARRAY_FILTER_USE_KEY);
        $this->assertBitrix24AllResultItemFieldsHasValidTypeAnnotation(
            $systemFields,
            EpicItemResult::class
        );
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
     * Test Epic service fields method
     *
     * @throws BaseException
     * @throws TransportException
     */
    public function testFields(): void
    {
        $epicFieldsResult = $this->epicService->getFields();

        // Get the raw result to examine structure
        $this->assertNotNull($epicFieldsResult);

        // Check if we can access field descriptions
        $fields = $epicFieldsResult->getFieldsDescription();
        $this->assertIsArray($fields);
        $this->assertNotEmpty($fields);

        // Check expected Epic fields based on API documentation
        $this->assertArrayHasKey('name', $fields);
        $this->assertArrayHasKey('description', $fields);
        $this->assertArrayHasKey('groupId', $fields);
        $this->assertArrayHasKey('color', $fields);
        $this->assertArrayHasKey('files', $fields);
        $this->assertArrayHasKey('createdBy', $fields);
        $this->assertArrayHasKey('modifiedBy', $fields);

        // Verify field types
        $this->assertEquals('string', $fields['name']['type']);
        $this->assertEquals('string', $fields['description']['type']);
        $this->assertEquals('integer', $fields['groupId']['type']);
        $this->assertEquals('string', $fields['color']['type']);
        $this->assertEquals('array', $fields['files']['type']);
        $this->assertEquals('integer', $fields['createdBy']['type']);
        $this->assertEquals('integer', $fields['modifiedBy']['type']);
    }

    /**
     * @throws BaseException
     * @throws TransportException
     */
    public function testCrudOperations(): void
    {
        $groupId = $this->getTestGroupId();

        // Test add epic
        $epicAddedResult = $this->epicService->add([
            'name' => 'Test Epic',
            'description' => 'This is a test epic for integration testing',
            'groupId' => $groupId,
            'color' => '#FF0000',
        ]);

        $epicId = $epicAddedResult->getId();
        self::$createdEpicIds[] = $epicId; // Track for cleanup
        $this->assertIsInt($epicId);
        $this->assertGreaterThan(0, $epicId);

        // Test get epic
        $epicResult = $this->epicService->get($epicId);
        $epic = $epicResult->epic();

        $this->assertInstanceOf(EpicItemResult::class, $epic);
        $this->assertEquals($epicId, $epic->id);
        $this->assertEquals('Test Epic', $epic->name);
        $this->assertEquals('This is a test epic for integration testing', $epic->description);
        $this->assertEquals($groupId, $epic->groupId);
        $this->assertEquals('#FF0000', $epic->color);

        // Test update epic
        $epicUpdatedResult = $this->epicService->update($epicId, [
            'name' => 'Updated Test Epic',
            'description' => 'Updated description for test epic',
            'color' => '#00FF00',
        ]);

        $this->assertTrue($epicUpdatedResult->isSuccess());

        // Verify update
        $getUpdatedResult = $this->epicService->get($epicId);
        $epicItemResult = $getUpdatedResult->epic();

        $this->assertEquals('Updated Test Epic', $epicItemResult->name);
        $this->assertEquals('Updated description for test epic', $epicItemResult->description);
        $this->assertEquals('#00FF00', $epicItemResult->color);

        // Test list epics
        $epicsResult = $this->epicService->list([], ['GROUP_ID' => $groupId]);
        $epics = $epicsResult->getEpics();

        $this->assertNotEmpty($epics);
        $foundEpic = null;
        foreach ($epics as $epic) {
            if ($epic->id === $epicId) {
                $foundEpic = $epic;
                break;
            }
        }

        $this->assertNotNull($foundEpic);
        $this->assertEquals('Updated Test Epic', $foundEpic->name);

        // Test delete epic
        $epicDeletedResult = $this->epicService->delete($epicId);
        $this->assertTrue($epicDeletedResult->isSuccess());

        // Verify deletion - should throw exception or return empty result
        try {
            $this->epicService->get($epicId);
            $this->fail('Expected exception when getting deleted epic');
        } catch (BaseException) {
            // Expected behavior - epic not found
            $this->assertTrue(true);
        }
    }

    /**
     * @throws BaseException
     * @throws TransportException
     */
    public function testListWithFilters(): void
    {
        $groupId = $this->getTestGroupId();

        // Create multiple epics
        $epic1Id = $this->epicService->add([
            'name' => 'Epic Filter Test 1',
            'description' => 'First epic for filter testing',
            'groupId' => $groupId,
            'color' => '#FF0000',
        ])->getId();
        self::$createdEpicIds[] = $epic1Id; // Track for cleanup

        $epic2Id = $this->epicService->add([
            'name' => 'Epic Filter Test 2',
            'description' => 'Second epic for filter testing',
            'groupId' => $groupId,
            'color' => '#00FF00',
        ])->getId();
        self::$createdEpicIds[] = $epic2Id; // Track for cleanup

        // Test list with group filter
        $epicsResult = $this->epicService->list([], ['GROUP_ID' => $groupId]);
        $epics = $epicsResult->getEpics();

        $this->assertGreaterThanOrEqual(2, count($epics));

        // Verify all returned epics belong to the correct group
        foreach ($epics as $epic) {
            $this->assertEquals($groupId, $epic->groupId);
        }

        // Test list with select fields
        $listSelectResult = $this->epicService->list([], ['GROUP_ID' => $groupId], ['ID', 'NAME']);
        $selectedEpics = $listSelectResult->getEpics();

        $this->assertNotEmpty($selectedEpics);
        foreach ($selectedEpics as $selectedEpic) {
            $this->assertNotNull($selectedEpic->id);
            $this->assertNotNull($selectedEpic->name);
        }

        // Cleanup
        $this->epicService->delete($epic1Id);
        $this->epicService->delete($epic2Id);
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
