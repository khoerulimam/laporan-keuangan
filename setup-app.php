<?php
/**
 * Setup Helper Script
 * Jalankan: php setup-app.php
 */

echo "\n=== Laporan Keuangan - Setup Helper ===\n\n";

$baseDir = __DIR__;
$viewsDir = $baseDir . '/resources/views';
$authDir = $viewsDir . '/auth';

// Step 1: Create auth directory
echo "1. Creating auth directory...\n";
if (!is_dir($authDir)) {
    mkdir($authDir, 0755, true);
    echo "   ✓ Directory created: $authDir\n";
} else {
    echo "   ✓ Directory already exists\n";
}

// Step 2: Move login file
echo "\n2. Moving login view file...\n";
$sourceFile = $viewsDir . '/auth.login.blade.php';
$targetFile = $authDir . '/login.blade.php';

if (file_exists($sourceFile)) {
    if (file_exists($targetFile)) {
        echo "   ✓ File already exists at target location\n";
    } else {
        if (rename($sourceFile, $targetFile)) {
            echo "   ✓ File moved successfully\n";
        } else {
            echo "   ✗ Failed to move file\n";
        }
    }
} else {
    echo "   ℹ Source file not found (may have been moved already)\n";
}

// Step 3: Run artisan commands
echo "\n3. Running database migrations...\n";
system('php artisan migrate --force --quiet');
echo "   ✓ Migrations completed\n";

echo "\n4. Seeding demo user...\n";
system('php artisan db:seed --force --quiet');
echo "   ✓ Database seeded\n";

// Step 5: Cache clearing
echo "\n5. Clearing caches...\n";
system('php artisan config:clear --quiet');
system('php artisan cache:clear --quiet');
system('php artisan view:clear --quiet');
echo "   ✓ Caches cleared\n";

echo "\n=== Setup Complete! ===\n\n";
echo "Demo credentials:\n";
echo "  Email: demo@demo.com\n";
echo "  Password: password\n\n";
echo "Visit: http://localhost\n\n";
