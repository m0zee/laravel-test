#!/usr/bin/env php
<?php

/**
 * Google Wallet Configuration Test Script
 *
 * This script verifies that your Google Wallet integration is properly configured.
 * Run: php test-google-wallet-config.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n=== Google Wallet Configuration Test ===\n\n";

// Test 1: Check Issuer ID
echo "1. Checking Issuer ID...\n";
$issuerId = config('passes.google.issuer_id');
if ($issuerId) {
    echo "   ✓ Issuer ID found: {$issuerId}\n";
} else {
    echo "   ✗ Issuer ID not configured!\n";
    echo "   Add GOOGLE_WALLET_ISSUER_ID to your .env file\n";
}

// Test 2: Check Merchant ID
echo "\n2. Checking Merchant ID...\n";
$merchantId = config('passes.google.merchant_id');
if ($merchantId) {
    echo "   ✓ Merchant ID found: {$merchantId}\n";
} else {
    echo "   ⚠ Merchant ID not configured (optional)\n";
}

// Test 3: Check Service Credentials File
echo "\n3. Checking Service Credentials File...\n";
$credentialsPath = config('passes.google.credentials');
if (file_exists($credentialsPath)) {
    echo "   ✓ Credentials file found: {$credentialsPath}\n";

    // Validate JSON
    $jsonContent = file_get_contents($credentialsPath);
    $credentials = json_decode($jsonContent, true);

    if ($credentials && isset($credentials['client_email'], $credentials['private_key'])) {
        echo "   ✓ Credentials file is valid JSON\n";
        echo "   ✓ Client Email: {$credentials['client_email']}\n";
        echo "   ✓ Project ID: " . ($credentials['project_id'] ?? 'N/A') . "\n";
    } else {
        echo "   ✗ Credentials file is invalid or corrupted\n";
    }
} else {
    echo "   ✗ Credentials file not found at: {$credentialsPath}\n";
    echo "   Make sure the JSON file exists in the public folder\n";
}

// Test 4: Check Origins
echo "\n4. Checking Origins Configuration...\n";
$origins = config('passes.google.origins');
if ($origins && is_array($origins) && count($origins) > 0) {
    echo "   ✓ Origins configured:\n";
    foreach ($origins as $origin) {
        echo "     - {$origin}\n";
    }
} else {
    echo "   ⚠ No origins configured\n";
}

// Test 5: Check App URL
echo "\n5. Checking App URL...\n";
$appUrl = config('app.url');
echo "   App URL: {$appUrl}\n";

// Test 6: Test Class ID Generation
echo "\n6. Testing Class ID Format...\n";
if ($issuerId) {
    $classId = "{$issuerId}.generic-pass-class";
    echo "   Generated Class ID: {$classId}\n";

    if (strlen($classId) > 0 && strpos($classId, '.') !== false) {
        echo "   ✓ Class ID format is valid\n";
    } else {
        echo "   ✗ Class ID format is invalid\n";
    }
}

// Test 7: Check Service Class
echo "\n7. Checking GoogleWalletService...\n";
try {
    $service = app(\App\Services\GoogleWalletService::class);
    echo "   ✓ GoogleWalletService can be instantiated\n";
} catch (\Exception $e) {
    echo "   ✗ Error instantiating GoogleWalletService:\n";
    echo "     {$e->getMessage()}\n";
}

// Summary
echo "\n=== Configuration Test Complete ===\n";
echo "\nNext Steps:\n";
echo "1. Visit http://localhost/wallet to test the web interface\n";
echo "2. Or test via API: POST http://localhost/wallet/create-class\n";
echo "3. Generate a pass: POST http://localhost/wallet/generate\n\n";

