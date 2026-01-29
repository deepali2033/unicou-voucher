<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\Schema;
echo "is_active in users: " . (Schema::hasColumn('users', 'is_active') ? 'YES' : 'NO') . "\n";
