<?php

namespace Tests\Unit;

use Tests\TestCase;

class UserSyncScriptTest extends TestCase
{
    /**
     * Test that sync script file exists and has proper structure
     */
    public function test_sync_script_exists_and_has_proper_structure()
    {
        $scriptPath = base_path('sync_users_from_koa.php');

        // Test file exists
        $this->assertFileExists($scriptPath, 'User sync script should exist');

        // Test file is readable
        $this->assertTrue(is_readable($scriptPath), 'Script should be readable');

        // Get script content
        $content = file_get_contents($scriptPath);

        // Test basic PHP structure
        $this->assertStringStartsWith('<?php', $content, 'Script should start with PHP opening tag');
        $this->assertNotEmpty($content, 'Script should not be empty');
    }

    /**
     * Test script contains required functions
     */
    public function test_script_contains_required_functions()
    {
        $scriptPath = base_path('sync_users_from_koa.php');
        $content = file_get_contents($scriptPath);

        $requiredFunctions = [
            'createConnection',
            'logMessage',
            'getTableColumns',
            'arraysAreDifferent'
        ];

        foreach ($requiredFunctions as $function) {
            $this->assertStringContainsString(
                "function {$function}",
                $content,
                "Script should contain {$function} function"
            );
        }
    }

    /**
     * Test script contains required database configurations
     */
    public function test_script_contains_database_configurations()
    {
        $scriptPath = base_path('sync_users_from_koa.php');
        $content = file_get_contents($scriptPath);

        $requiredConfigs = [
            'koa_services',
            'koa_source',
            'koaservices',
            'KOA',
            '127.0.0.1',
            '3306',
            'root'
        ];

        foreach ($requiredConfigs as $config) {
            $this->assertStringContainsString(
                $config,
                $content,
                "Script should contain {$config} configuration"
            );
        }
    }

    /**
     * Test script supports command line arguments
     */
    public function test_script_supports_command_line_arguments()
    {
        $scriptPath = base_path('sync_users_from_koa.php');
        $content = file_get_contents($scriptPath);

        $requiredOptions = [
            '--dry-run',
            '--force',
            '$argv'
        ];

        foreach ($requiredOptions as $option) {
            $this->assertStringContainsString(
                $option,
                $content,
                "Script should support {$option} option"
            );
        }
    }

    /**
     * Test script contains SQL operations
     */
    public function test_script_contains_sql_operations()
    {
        $scriptPath = base_path('sync_users_from_koa.php');
        $content = file_get_contents($scriptPath);

        $requiredSqlOperations = [
            'INSERT INTO users',
            'UPDATE users',
            'SELECT * FROM users',
            'beginTransaction',
            'commit',
            'rollBack'
        ];

        foreach ($requiredSqlOperations as $operation) {
            $this->assertStringContainsString(
                $operation,
                $content,
                "Script should contain {$operation} SQL operation"
            );
        }
    }

    /**
     * Test script has error handling
     */
    public function test_script_has_error_handling()
    {
        $scriptPath = base_path('sync_users_from_koa.php');
        $content = file_get_contents($scriptPath);

        $errorHandlingElements = [
            'try {',
            'catch',
            'Exception',
            'PDOException',
            'logMessage',
            'ERROR'
        ];

        foreach ($errorHandlingElements as $element) {
            $this->assertStringContainsString(
                $element,
                $content,
                "Script should contain {$element} for error handling"
            );
        }
    }

    /**
     * Test script configuration values are appropriate
     */
    public function test_script_configuration_values()
    {
        $scriptPath = base_path('sync_users_from_koa.php');
        $content = file_get_contents($scriptPath);

        // Test database names match expected values
        $this->assertStringContainsString("'database' => 'koaservices'", $content);
        $this->assertStringContainsString("'database' => 'KOA'", $content);

        // Test connection settings
        $this->assertStringContainsString("'host' => '127.0.0.1'", $content);
        $this->assertStringContainsString("'port' => 3306", $content);
        $this->assertStringContainsString("'username' => 'root'", $content);
        $this->assertStringContainsString("'charset' => 'utf8mb4'", $content);
    }

    /**
     * Test script has proper logging functionality
     */
    public function test_script_has_logging_functionality()
    {
        $scriptPath = base_path('sync_users_from_koa.php');
        $content = file_get_contents($scriptPath);

        $loggingElements = [
            'logMessage',
            'INFO',
            'ERROR',
            'WARN',
            'date(',
            'echo'
        ];

        foreach ($loggingElements as $element) {
            $this->assertStringContainsString(
                $element,
                $content,
                "Script should contain {$element} for logging"
            );
        }

        // Test for specific log messages
        $expectedLogMessages = [
            'Starting user synchronization',
            'Connecting to databases',
            'Sync Completed Successfully',
            'DRY RUN MODE'
        ];

        foreach ($expectedLogMessages as $message) {
            $this->assertStringContainsString(
                $message,
                $content,
                "Script should contain log message: {$message}"
            );
        }
    }

    /**
     * Test script file size is reasonable (not empty, not too large)
     */
    public function test_script_file_size_is_reasonable()
    {
        $scriptPath = base_path('sync_users_from_koa.php');
        $fileSize = filesize($scriptPath);

        // Test file is not empty
        $this->assertGreaterThan(0, $fileSize, 'Script file should not be empty');

        // Test file is not unreasonably large (should be under 100KB for a sync script)
        $this->assertLessThan(102400, $fileSize, 'Script file should not be unreasonably large');

        // Test file has reasonable minimum size (at least 1KB for a functional script)
        $this->assertGreaterThan(1024, $fileSize, 'Script file should have reasonable minimum size');
    }

    /**
     * Test script has proper documentation
     */
    public function test_script_has_documentation()
    {
        $scriptPath = base_path('sync_users_from_koa.php');
        $content = file_get_contents($scriptPath);

        $documentationElements = [
            'Standalone script to sync users',
            'Usage:',
            'This script will:',
            'php sync_users_from_koa.php'
        ];

        foreach ($documentationElements as $element) {
            $this->assertStringContainsString(
                $element,
                $content,
                "Script should contain documentation element: {$element}"
            );
        }
    }
}
