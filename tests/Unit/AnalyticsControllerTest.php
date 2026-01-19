<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\Admin\AnalyticsController;
use Mockery;

class AnalyticsControllerTest extends TestCase
{
    public function test_analytics_controller_exists()
    {
        $this->assertTrue(class_exists('App\Http\Controllers\Admin\AnalyticsController'));
    }

    public function test_analytics_controller_has_index_method()
    {
        $controller = new AnalyticsController();
        $this->assertTrue(method_exists($controller, 'index'));
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
