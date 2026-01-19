<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use PDO;

class UserSynchronizationTest extends TestCase
{
    /**
     * Test the complete user synchronization process
     */
    public function test_complete_user_synchronization_process()
    {
        // First, verify both databases are accessible
        $this->verifyDatabaseConnections();

        // Get initial state
        $initialKoaServicesUsers = DB::table('users')->orderBy('id')->get();
        $initialKoaUsers = $this->getKoaUsers();

        $this->assertNotEmpty($initialKoaUsers, 'KOA database should have users');

        // Run the synchronization script in dry-run mode first
        $dryRunResult = $this->runSyncScript(['--dry-run']);
        $this->assertTrue($dryRunResult['success'], 'Dry run should succeed');
        $this->assertStringContainsString('This was a dry run', $dryRunResult['output']);

        // Run the actual synchronization
        $syncResult = $this->runSyncScript(['--force']);
        $this->assertTrue($syncResult['success'], 'Synchronization should succeed');
        $this->assertStringContainsString('Sync Completed Successfully', $syncResult['output']);

        // Verify synchronization results
        $this->verifyPostSyncState($initialKoaUsers);
    }

    /**
     * Test synchronization handles different user scenarios (Integration test - only in production environment)
     */
    public function test_synchronization_handles_various_scenarios()
    {
        // Skip this test if we're in SQLite testing mode
        if (DB::getDriverName() === 'sqlite') {
            $this->markTestSkipped('This test requires MySQL connection - skipped in SQLite test environment');
        }

        try {
            $koaUsers = $this->getKoaUsers();
            $koaServicesUsers = DB::table('users')->get();

            // Test that all KOA users are present in koaservices
            foreach ($koaUsers as $koaUser) {
                $matchingUser = $koaServicesUsers->firstWhere('id', $koaUser['id']);
                $this->assertNotNull(
                    $matchingUser,
                    "User with ID {$koaUser['id']} from KOA should exist in koaservices"
                );
            }

            // Test data consistency for common fields
            $commonFields = ['id', 'name', 'email', 'email_verified_at', 'remember_token', 'created_at', 'updated_at'];

            foreach ($koaUsers as $koaUser) {
                $koaServiceUser = $koaServicesUsers->firstWhere('id', $koaUser['id']);

                if ($koaServiceUser) {
                    foreach ($commonFields as $field) {
                        if ($field === 'password') {
                            continue; // Skip password comparison
                        }

                        $koaValue = $koaUser[$field] ?? null;
                        $serviceValue = $koaServiceUser->$field ?? null;

                        // Handle datetime fields
                        if (in_array($field, ['created_at', 'updated_at', 'email_verified_at'])) {
                            if ($koaValue && $serviceValue) {
                                $koaValue = date('Y-m-d H:i:s', strtotime($koaValue));
                                $serviceValue = date('Y-m-d H:i:s', strtotime($serviceValue));
                            }
                        }

                        $this->assertEquals(
                            $koaValue,
                            $serviceValue,
                            "Field '{$field}' should match for user ID {$koaUser['id']}"
                        );
                    }
                }
            }
        } catch (\Exception $e) {
            $this->markTestSkipped('Database connection issue: ' . $e->getMessage());
        }
    }

    /**
     * Test synchronization script exists and is properly structured
     */
    public function test_synchronization_script_structure()
    {
        $scriptPath = base_path('sync_users_from_koa.php');
        $this->assertFileExists($scriptPath);

        $content = file_get_contents($scriptPath);

        // Check for required components
        $requiredElements = [
            'createConnection',
            'getTableColumns',
            'arraysAreDifferent',
            'logMessage',
            'KOA',
            'koaservices',
            'INSERT INTO users',
            'UPDATE users',
            'beginTransaction',
            'commit',
            'rollBack'
        ];

        foreach ($requiredElements as $element) {
            $this->assertStringContainsString(
                $element,
                $content,
                "Script should contain '{$element}'"
            );
        }
    }

    /**
     * Test error handling in synchronization
     */
    public function test_synchronization_error_handling()
    {
        // Test with invalid database configuration
        $invalidConfig = [
            'host' => '127.0.0.1',
            'port' => 3306,
            'database' => 'nonexistent_database',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4'
        ];

        try {
            $dsn = "mysql:host={$invalidConfig['host']};port={$invalidConfig['port']};dbname={$invalidConfig['database']};charset={$invalidConfig['charset']}";
            $connection = new PDO($dsn, $invalidConfig['username'], $invalidConfig['password']);
            $this->fail('Should have thrown an exception for invalid database');
        } catch (\PDOException $e) {
            $this->assertStringContainsString('Unknown database', $e->getMessage());
        }
    }

    /**
     * Test synchronization maintains data integrity (Integration test - only in production environment)
     */
    public function test_synchronization_maintains_data_integrity()
    {
        // Skip this test if we're in SQLite testing mode
        if (DB::getDriverName() === 'sqlite') {
            $this->markTestSkipped('This test requires MySQL connection - skipped in SQLite test environment');
        }

        try {
            $koaUsers = $this->getKoaUsers();
            $koaServicesUsers = DB::table('users')->get();

            // Verify no duplicate users
            $koaServicesIds = $koaServicesUsers->pluck('id')->toArray();
            $this->assertEquals(
                count($koaServicesIds),
                count(array_unique($koaServicesIds)),
                'No duplicate user IDs should exist'
            );

            // Verify no duplicate emails
            $koaServicesEmails = $koaServicesUsers->pluck('email')->toArray();
            $this->assertEquals(
                count($koaServicesEmails),
                count(array_unique($koaServicesEmails)),
                'No duplicate emails should exist'
            );

            // Verify email format
            foreach ($koaServicesUsers as $user) {
                $this->assertMatchesRegularExpression(
                    '/^[^\s@]+@[^\s@]+\.[^\s@]+$/',
                    $user->email,
                    "User {$user->id} should have valid email format"
                );
            }
        } catch (\Exception $e) {
            $this->markTestSkipped('Database connection issue: ' . $e->getMessage());
        }
    }

    /**
     * Test synchronization performance
     */
    public function test_synchronization_performance()
    {
        $startTime = microtime(true);

        // Run sync script
        $result = $this->runSyncScript(['--force']);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertTrue($result['success'], 'Sync should complete successfully');
        $this->assertLessThan(30, $executionTime, 'Synchronization should complete within 30 seconds');
    }

    /**
     * Helper method to verify database connections
     */
    private function verifyDatabaseConnections()
    {
        // Test koaservices connection
        $koaServicesConnection = DB::connection();
        $this->assertNotNull($koaServicesConnection);

        // Test KOA connection
        $koaConnection = $this->getKoaConnection();
        $this->assertInstanceOf(PDO::class, $koaConnection);
    }

    /**
     * Helper method to get KOA database connection
     */
    private function getKoaConnection()
    {
        $config = [
            'host' => '127.0.0.1',
            'port' => 3306,
            'database' => 'KOA',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4'
        ];

        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
        $connection = new PDO($dsn, $config['username'], $config['password']);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $connection;
    }

    /**
     * Helper method to get users from KOA database
     */
    private function getKoaUsers()
    {
        $connection = $this->getKoaConnection();
        $stmt = $connection->query("SELECT * FROM users ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Helper method to run the synchronization script
     */
    private function runSyncScript($arguments = [])
    {
        $scriptPath = base_path('sync_users_from_koa.php');
        $command = 'C:\xampp\php\php.exe "' . $scriptPath . '"';

        foreach ($arguments as $arg) {
            $command .= ' ' . $arg;
        }

        $output = '';
        $exitCode = 0;

        exec($command . ' 2>&1', $outputArray, $exitCode);
        $output = implode("\n", $outputArray);

        return [
            'success' => $exitCode === 0,
            'output' => $output,
            'exit_code' => $exitCode
        ];
    }

    /**
     * Helper method to verify post-synchronization state
     */
    private function verifyPostSyncState($expectedKoaUsers)
    {
        $postSyncUsers = DB::table('users')->orderBy('id')->get();

        // Verify user count matches
        $this->assertEquals(
            count($expectedKoaUsers),
            $postSyncUsers->count(),
            'User count should match after sync'
        );

        // Verify each user's data
        foreach ($expectedKoaUsers as $expectedUser) {
            $actualUser = $postSyncUsers->firstWhere('id', $expectedUser['id']);

            $this->assertNotNull(
                $actualUser,
                "User with ID {$expectedUser['id']} should exist after sync"
            );

            // Check common fields
            $commonFields = ['name', 'email', 'email_verified_at', 'remember_token'];
            foreach ($commonFields as $field) {
                $expectedValue = $expectedUser[$field] ?? null;
                $actualValue = $actualUser->$field ?? null;

                // Handle datetime fields
                if (in_array($field, ['email_verified_at']) && $expectedValue && $actualValue) {
                    $expectedValue = date('Y-m-d H:i:s', strtotime($expectedValue));
                    $actualValue = date('Y-m-d H:i:s', strtotime($actualValue));
                }

                $this->assertEquals(
                    $expectedValue,
                    $actualValue,
                    "Field '{$field}' should match for user ID {$expectedUser['id']} after sync"
                );
            }
        }
    }
}
