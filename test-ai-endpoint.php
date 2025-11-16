<?php
/**
 * Quick test to see what the AI endpoint returns
 * Usage: php test-ai-endpoint.php
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

// Test 1: Check if config loads
echo "=== Test 1: Check AI Configuration ===\n";
$apiKey = config('services.ai.key');
$endpoint = config('services.ai.endpoint');
$model = config('services.ai.model');

echo "API Key: " . ($apiKey ? "✅ SET" : "❌ EMPTY") . "\n";
echo "Endpoint: " . ($endpoint ? $endpoint : "❌ MISSING") . "\n";
echo "Model: " . ($model ? $model : "❌ MISSING") . "\n\n";

// Test 2: Check database connection
echo "=== Test 2: Check Database Connection ===\n";
try {
    $conn = \Illuminate\Support\Facades\DB::connection();
    $conn->getPdo();
    echo "✅ Database connection OK\n";
} catch (\Exception $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test 3: Test Route ===\n";
// Test if we can reach the route
echo "Route name: ai.chat\n";
echo "Route method: POST\n";
echo "Route path: " . route('ai.chat') . "\n";

echo "\n=== Test 4: Check for Errors ===\n";
if (!$apiKey) {
    echo "⚠️  WARNING: SERVICES_AI_KEY is not set in .env!\n";
    echo "   Add your OpenAI key: SERVICES_AI_KEY=sk-...\n";
    echo "   Then restart: php artisan serve\n";
} else {
    echo "✅ API Key is configured\n";
}
