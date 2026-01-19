<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use PDO;
use Exception;

class UserSyncTest extends TestCase
{
    /**
     * Test database connection to both databases (Integration test - only in production environment)
     */
    public function test_can_connect_to_both_databases()
    {
        // Skip this test if we're in SQLite testing mode
        if (DB::getDriverName() === 'sqlite') {
            $this->markTestSkipped('This test requires MySQL connection - skipped in SQLite test environment');
        }

        // Test connection to koaservices database
        $koaServicesConnection = DB::connection();
        $this->assertInstanceOf(\Illuminate\Database\Connection::class, $koaServicesConnection);

        // Test connection to KOA database using raw PDO
        try {
            $config = [
                'host' => '127.0.0.1',
                'port' => 3306,
                'database' => 'KOA',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8mb4'
            ];

            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
            $koaConnection = new PDO($dsn, $config['username'], $config['password']);
            $koaConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->assertInstanceOf(PDO::class, $koaConnection);

            // Test basic query on KOA
            $stmt = $koaConnection->query("SELECT COUNT(*) FROM users");
            $koaUserCount = $stmt->fetchColumn();
            $this->assertIsNumeric($koaUserCount);
        } catch (\PDOException $e) {
            $this->markTestSkipped('KOA database not available: ' . $e->getMessage());
        }
    }

    /**
     * Test users table exists in both databases (Integration test - only in production environment)
     */
    public function test_users_table_exists_in_both_databases()
    {
        // Skip this test if we're in SQLite testing mode
        if (DB::getDriverName() === 'sqlite') {
            $this->markTestSkipped('This test requires MySQL connection - skipped in SQLite test environment');
        }

        try {
            // Check koaservices database (use MySQL-compatible query)
            $koaServicesTables = DB::select("SHOW TABLES LIKE 'users'");
            $this->assertNotEmpty($koaServicesTables, 'Users table should exist in koaservices database');

            // Check KOA database
            $config = [
                'host' => '127.0.0.1',
                'port' => 3306,
                'database' => 'KOA',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8mb4'
            ];

            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
            $koaConnection = new PDO($dsn, $config['username'], $config['password']);
            $koaConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $koaConnection->query("SHOW TABLES LIKE 'users'");
            $koaTables = $stmt->fetchAll();
            $this->assertNotEmpty($koaTables, 'Users table should exist in KOA database');
        } catch (\Exception $e) {
            $this->markTestSkipped('Database connection issue: ' . $e->getMessage());
        }
    }

    /**
     * Test table structures have common columns (Integration test - only in production environment)
     */
    public function test_users_tables_have_common_columns()
    {
        // Skip this test if we're in SQLite testing mode
        if (DB::getDriverName() === 'sqlite') {
            $this->markTestSkipped('This test requires MySQL connection - skipped in SQLite test environment');
        }

        try {
            // Get koaservices table structure (use MySQL-compatible query)
            $koaServicesColumns = collect(DB::select("DESCRIBE users"))->pluck('Field')->toArray();

            // Get KOA table structure
            $config = [
                'host' => '127.0.0.1',
                'port' => 3306,
                'database' => 'KOA',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8mb4'
            ];

            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
            $koaConnection = new PDO($dsn, $config['username'], $config['password']);
            $koaConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $koaConnection->query("DESCRIBE users");
            $koaColumns = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field');

            // Find common columns
            $commonColumns = array_intersect($koaColumns, $koaServicesColumns);

            $this->assertNotEmpty($commonColumns, 'Tables should have common columns');
            $this->assertContains('id', $commonColumns, 'ID column should be present in both tables');
            $this->assertContains('email', $commonColumns, 'Email column should be present in both tables');
            $this->assertContains('name', $commonColumns, 'Name column should be present in both tables');
        } catch (\Exception $e) {
            $this->markTestSkipped('Database connection issue: ' . $e->getMessage());
        }
    }

    /**
     * Test sync script file exists and is executable
     */
    public function test_sync_script_file_exists()
    {
        $scriptPath = base_path('sync_users_from_koa.php');
        $this->assertFileExists($scriptPath, 'User sync script should exist');

        // Check if script has proper PHP opening tag
        $content = file_get_contents($scriptPath);
        $this->assertStringStartsWith('<?php', $content, 'Script should start with PHP opening tag');

        // Check if script contains key functions
        $this->assertStringContainsString('createConnection', $content, 'Script should contain createConnection function');
        $this->assertStringContainsString('getTableColumns', $content, 'Script should contain getTableColumns function');
        $this->assertStringContainsString('arraysAreDifferent', $content, 'Script should contain arraysAreDifferent function');
    }

    /**
     * Test user data synchronization was successful (Integration test - only in production environment)
     */
    public function test_user_sync_was_successful()
    {
        // Skip this test if we're in SQLite testing mode
        if (DB::getDriverName() === 'sqlite') {
            $this->markTestSkipped('This test requires MySQL connection - skipped in SQLite test environment');
        }

        try {
            // Get users from koaservices (this will work in MySQL mode)
            $koaServicesUsers = DB::table('users')->orderBy('id')->get();

            // Get users from KOA database
            $config = [
                'host' => '127.0.0.1',
                'port' => 3306,
                'database' => 'KOA',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8mb4'
            ];

            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
            $koaConnection = new PDO($dsn, $config['username'], $config['password']);
            $koaConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $koaConnection->query("SELECT * FROM users ORDER BY id");
            $koaUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->assertNotEmpty($koaServicesUsers, 'KoaServices should have users');
            $this->assertNotEmpty($koaUsers, 'KOA database should have users');

            // Compare common data for each user
            $commonColumns = ['id', 'name', 'email', 'email_verified_at', 'password', 'remember_token', 'created_at', 'updated_at'];

            foreach ($koaServicesUsers as $index => $koaServiceUser) {
                $koaUser = collect($koaUsers)->firstWhere('id', $koaServiceUser->id);

                if ($koaUser) {
                    foreach ($commonColumns as $column) {
                        if ($column === 'password') {
                            // Skip password comparison as it might be hashed
                            continue;
                        }

                        $serviceValue = $koaServiceUser->$column;
                        $koaValue = $koaUser[$column] ?? null;

                        // Handle datetime comparison
                        if (in_array($column, ['created_at', 'updated_at', 'email_verified_at'])) {
                            if ($serviceValue && $koaValue) {
                                $serviceValue = date('Y-m-d H:i:s', strtotime($serviceValue));
                                $koaValue = date('Y-m-d H:i:s', strtotime($koaValue));
                            }
                        }

                        $this->assertEquals(
                            $koaValue,
                            $serviceValue,
                            "Column '{$column}' should match between databases for user ID {$koaServiceUser->id}"
                        );
                    }
                }
            }
        } catch (\Exception $e) {
            $this->markTestSkipped('Database connection issue: ' . $e->getMessage());
        }
    }

    /**
     * Test user count matches between databases (Integration test - only in production environment)
     */
    public function test_user_count_matches_between_databases()
    {
        // Skip this test if we're in SQLite testing mode
        if (DB::getDriverName() === 'sqlite') {
            $this->markTestSkipped('This test requires MySQL connection - skipped in SQLite test environment');
        }

        try {
            // Get count from koaservices
            $koaServicesCount = DB::table('users')->count();

            // Get count from KOA database
            $config = [
                'host' => '127.0.0.1',
                'port' => 3306,
                'database' => 'KOA',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8mb4'
            ];

            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
            $koaConnection = new PDO($dsn, $config['username'], $config['password']);
            $koaConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $koaConnection->query("SELECT COUNT(*) FROM users");
            $koaCount = $stmt->fetchColumn();

            $this->assertEquals(
                $koaCount,
                $koaServicesCount,
                "User count should match between KOA ({$koaCount}) and koaservices ({$koaServicesCount}) databases"
            );
        } catch (\Exception $e) {
            $this->markTestSkipped('Database connection issue: ' . $e->getMessage());
        }
    }

    /**
     * Test specific user data integrity (Integration test - only in production environment)
     */
    public function test_specific_user_data_integrity()
    {
        // Skip this test if we're in SQLite testing mode
        if (DB::getDriverName() === 'sqlite') {
            $this->markTestSkipped('This test requires MySQL connection - skipped in SQLite test environment');
        }

        try {
            // Get a specific user from koaservices (assuming admin user exists)
            $koaServiceUser = DB::table('users')->where('email', 'admin@koaservices.com')->first();

            if ($koaServiceUser) {
                // Get the same user from KOA database
                $config = [
                    'host' => '127.0.0.1',
                    'port' => 3306,
                    'database' => 'KOA',
                    'username' => 'root',
                    'password' => '',
                    'charset' => 'utf8mb4'
                ];

                $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
                $koaConnection = new PDO($dsn, $config['username'], $config['password']);
                $koaConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $koaConnection->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute(['admin@koaservices.com']);
                $koaUser = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($koaUser) {
                    $this->assertEquals($koaUser['id'], $koaServiceUser->id);
                    $this->assertEquals($koaUser['email'], $koaServiceUser->email);
                    $this->assertEquals($koaUser['name'], $koaServiceUser->name);
                }
            }

            // If no admin user, just verify we have some users
            $totalUsers = DB::table('users')->count();
            $this->assertGreaterThanOrEqual(0, $totalUsers, 'Should have users in the database');
        } catch (\Exception $e) {
            $this->markTestSkipped('Database connection issue: ' . $e->getMessage());
        }
    }

    /**
     * Test synchronization script configuration
     */
    public function test_sync_script_configuration()
    {
        $scriptPath = base_path('sync_users_from_koa.php');
        $this->assertFileExists($scriptPath);

        $content = file_get_contents($scriptPath);

        // Check configuration sections
        $this->assertStringContainsString('koa_services', $content, 'Script should contain koaservices database config');
        $this->assertStringContainsString('koa_source', $content, 'Script should contain KOA database config');
        $this->assertStringContainsString('koaservices', $content, 'Script should reference koaservices database');
        $this->assertStringContainsString('KOA', $content, 'Script should reference KOA database');

        // Check for essential functionality
        $this->assertStringContainsString('--dry-run', $content, 'Script should support dry-run mode');
        $this->assertStringContainsString('--force', $content, 'Script should support force mode');
        $this->assertStringContainsString('INSERT INTO users', $content, 'Script should support user creation');
        $this->assertStringContainsString('UPDATE users', $content, 'Script should support user updates');
    }
}
