<?php
/**
 * Environment Variable Checker Script for Email Configuration
 *
 * This script verifies that all required Resend environment variables are set.
 * Run from console: php check_email_env.php
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(404);
    exit;
}

// ANSI color codes for terminal output
define('COLOR_GREEN', "\033[32m");
define('COLOR_RED', "\033[31m");
define('COLOR_YELLOW', "\033[33m");
define('COLOR_BLUE', "\033[34m");
define('COLOR_CYAN', "\033[36m");
define('COLOR_RESET', "\033[0m");
define('COLOR_BOLD', "\033[1m");

/**
 * Print colored text to console
 */
function printColor($text, $color = COLOR_RESET)
{
    echo $color . $text . COLOR_RESET . PHP_EOL;
}

/**
 * Print section header
 */
function printHeader($title)
{
    echo PHP_EOL;
    echo COLOR_BLUE . str_repeat('=', 70) . COLOR_RESET . PHP_EOL;
    echo COLOR_BOLD . COLOR_CYAN . "  $title" . COLOR_RESET . PHP_EOL;
    echo COLOR_BLUE . str_repeat('=', 70) . COLOR_RESET . PHP_EOL;
}

/**
 * Check environment variable
 */
function checkEnvVar($name, $required = true)
{
    $value = getenv($name);
    $isSet = $value !== false && $value !== '';

    $status = $isSet ? COLOR_GREEN . 'OK' : COLOR_RED . 'MISSING';
    $reqLabel = $required ? COLOR_RED . ' [REQUIRED]' : COLOR_YELLOW . ' [OPTIONAL]';

    if ($isSet) {
        // For sensitive data, just show first few chars
        if (in_array($name, ['RESEND_API_KEY'])) {
            $displayValue = substr($value, 0, 8) . '...' . substr($value, -4);
        } else {
            $displayValue = $value;
        }
        printf("  %-40s %s %s %s\n", $name, $status, COLOR_CYAN . $displayValue, $reqLabel . COLOR_RESET);
    } else {
        printf("  %-40s %s%s\n", $name, $status, $reqLabel . COLOR_RESET);
    }

    return $isSet;
}

// ========================================
// MAIN SCRIPT
// ========================================

echo PHP_EOL;
echo COLOR_BOLD . COLOR_CYAN . "Resend Environment Variable Checker" . COLOR_RESET . PHP_EOL;

printHeader("Checking .env File");

$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    printColor("  OK: .env file found at: $envFile", COLOR_GREEN);

    // Load the .env file manually for checking
    $envContents = file_get_contents($envFile);

    if (empty($envContents)) {
        printColor("  WARN: .env file is empty", COLOR_YELLOW);
    } else {
        $lineCount = count(explode("\n", $envContents));
        echo "  File contains $lineCount lines" . PHP_EOL;
    }
} else {
    printColor("  FAIL: .env file not found at: $envFile", COLOR_RED);
    echo "  Please create a .env file based on .env.example" . PHP_EOL;
}

printHeader("Resend Environment Variables");

$requiredVars = [
    'RESEND_API_KEY' => true,
    'RESEND_API_URL' => false,
    'RESEND_FROM_EMAIL' => true,
    'RESEND_FROM_NAME' => false,
    'EMAIL_DEBUG' => false
];

$results = [];
foreach ($requiredVars as $var => $required) {
    $results[$var] = checkEnvVar($var, $required);
}

printHeader("Configuration Summary");

$allRequiredSet = true;
$requiredResults = array_filter($requiredVars, function($required) { return $required; });

foreach ($requiredResults as $var => $required) {
    if (!$results[$var]) {
        $allRequiredSet = false;
        break;
    }
}

if ($allRequiredSet) {
    printColor("  OK: All required Resend environment variables are configured", COLOR_GREEN);
} else {
    printColor("  FAIL: Some required Resend environment variables are missing", COLOR_RED);
}

echo PHP_EOL;

// Check optional variables
$optionalSetCount = 0;
foreach ($requiredVars as $var => $required) {
    if (!$required && $results[$var]) {
        $optionalSetCount++;
    }
}
$totalOptional = count(array_filter($requiredVars, function($r) { return !$r; }));

echo "  Optional variables configured: $optionalSetCount/$totalOptional" . PHP_EOL;

if ($optionalSetCount < $totalOptional) {
    printColor("  INFO: Some optional variables are not set. This is OK, defaults will be used.", COLOR_YELLOW);
}

printHeader("Next Steps");

if (!$allRequiredSet) {
    printColor("  Please add the missing variables to your .env file:", COLOR_YELLOW);
    echo PHP_EOL;

    echo "  Required variables:" . PHP_EOL;
    foreach ($requiredResults as $var => $required) {
        if (!$results[$var]) {
            echo "    $var=your_value_here" . PHP_EOL;
        }
    }
    echo PHP_EOL;

    echo "  Optional variables (recommended for better experience):" . PHP_EOL;
    foreach ($requiredVars as $var => $required) {
        if (!$required && !$results[$var]) {
            echo "    $var=your_value_here  # Optional" . PHP_EOL;
        }
    }

    echo PHP_EOL;
    printColor("  You can copy values from .env.example as a starting point", COLOR_CYAN);
} else {
    printColor("  OK: Your Resend configuration is complete", COLOR_GREEN);
    echo PHP_EOL;
    printColor("  You can now test your email configuration by running:", COLOR_CYAN);
    echo "    php test_email_config.php send-test your@email.com" . PHP_EOL;
    echo PHP_EOL;
    printColor("  Or just check the configuration:", COLOR_CYAN);
    echo "    php test_email_config.php" . PHP_EOL;
}

printHeader("Resend Setup Guide");

echo "  If you haven't configured Resend yet, follow these steps:" . PHP_EOL;
echo PHP_EOL;
echo "  1. Sign up for free at https://resend.com" . PHP_EOL;
echo "  2. Verify a sending domain or address" . PHP_EOL;
echo "  3. Create an API key" . PHP_EOL;
echo "  4. Add the credentials to your .env file:" . PHP_EOL;
echo "     - API Key" . PHP_EOL;
echo "     - From Email" . PHP_EOL;
echo "     - From Name (optional)" . PHP_EOL;
echo PHP_EOL;
printColor("  For more information, visit: https://resend.com/docs", COLOR_CYAN);

echo PHP_EOL;
echo COLOR_BLUE . str_repeat('=', 70) . COLOR_RESET . PHP_EOL;
echo PHP_EOL;
