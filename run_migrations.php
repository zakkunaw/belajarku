<?php

echo "=== Laravel Migration Script ===\n";

// Change to project directory
chdir('c:\xampp\htdocs\belajarku');

echo "Current directory: " . getcwd() . "\n";

// Check if artisan file exists
if (!file_exists('artisan')) {
    echo "❌ Artisan file not found!\n";
    exit(1);
}

echo "✅ Artisan file found\n";

// Test database connection first
echo "\n--- Testing Database Connection ---\n";
exec('php artisan tinker --execute="try { DB::connection()->getPdo(); echo \"Database connected successfully!\"; } catch(Exception \$e) { echo \"Database error: \" . \$e->getMessage(); }"', $output, $return_code);

foreach ($output as $line) {
    echo $line . "\n";
}

// Clear config cache
echo "\n--- Clearing Config Cache ---\n";
exec('php artisan config:clear 2>&1', $output, $return_code);
foreach ($output as $line) {
    echo $line . "\n";
}

// Run migrations
echo "\n--- Running Migrations ---\n";
exec('php artisan migrate --force 2>&1', $output, $return_code);

if (empty($output)) {
    echo "⚠️ No output from migrate command\n";
    
    // Try migrate:status instead
    echo "\n--- Checking Migration Status ---\n";
    exec('php artisan migrate:status 2>&1', $output, $return_code);
}

foreach ($output as $line) {
    echo $line . "\n";
}

echo "\n--- Migration Script Complete ---\n";
