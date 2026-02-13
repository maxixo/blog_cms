<?php
/**
 * Email Configuration Test Script
 *
 * This script checks Resend configuration and tests the email sending functionality.
 * Run from console: php test_email_config.php [send-test]
 *
 * Usage:
 *   php test_email_config.php              - Check configuration only
 *   php test_email_config.php send-test    - Check config and send a test email
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/services/ResendEmailService.php';
require_once __DIR__ . '/config/EmailConfig.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
 * Print configuration item
 */
function printConfig($label, $value, $required = false)
{
    $status = !empty($value);
    $statusLabel = $status ? 'OK' : 'MISSING';
    $valueDisplay = $status ? $value : '[NOT SET]';

    if ($required) {
        $label .= COLOR_RED . ' *required*';
    }

    $statusColor = $status ? COLOR_GREEN : COLOR_RED;
    $valueColor = $status ? COLOR_GREEN : COLOR_RED;
    printf("  %-45s %s %s%s\n", $label, $statusColor . $statusLabel, $valueColor . $valueDisplay, COLOR_RESET);
}

/**
 * Test Resend API connection
 */
function testResendConnection($apiKey, $apiBaseUrl)
{
    printHeader("Testing Resend API Connection");

    echo "  API Base URL: $apiBaseUrl" . PHP_EOL;
    echo "  API Key: " . (!empty($apiKey) ? COLOR_GREEN . 'SET' : COLOR_RED . 'NOT SET') . COLOR_RESET . PHP_EOL;
    echo PHP_EOL;

    if (empty($apiKey)) {
        printColor("  WARN: Cannot test connection: Missing API key", COLOR_YELLOW);
        return false;
    }

    $domainsUrl = rtrim($apiBaseUrl, '/') . '/domains';
    echo "  Testing connection..." . PHP_EOL;

    $ch = curl_init($domainsUrl);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    echo PHP_EOL;

    if ($curlError) {
        printColor("  FAIL: Connection Failed", COLOR_RED);
        echo "  cURL Error: $curlError" . PHP_EOL;
        return false;
    }

    echo "  HTTP Response Code: " . ($httpCode >= 200 && $httpCode < 300 ? COLOR_GREEN : COLOR_RED) . $httpCode . COLOR_RESET . PHP_EOL;
    echo "  Response: " . PHP_EOL;
    echo "  " . COLOR_CYAN . json_encode(json_decode($response), JSON_PRETTY_PRINT) . COLOR_RESET . PHP_EOL;

    if ($httpCode >= 200 && $httpCode < 300) {
        printColor("  OK: Connection Successful", COLOR_GREEN);
        return true;
    }

    printColor("  FAIL: Connection Failed", COLOR_RED);
    return false;
}

/**
 * Send a test email
 */
function sendTestEmail($testEmail)
{
    printHeader("Sending Test Email");

    echo "  Test Email Address: " . COLOR_CYAN . $testEmail . COLOR_RESET . PHP_EOL;
    echo PHP_EOL;

    $emailService = new ResendEmailService();

    echo "  Sending test email..." . PHP_EOL;
    $result = $emailService->sendWelcomeEmail($testEmail, 'Test User');

    echo PHP_EOL;
    echo "  Result: " . PHP_EOL;
    echo "  Success: " . ($result['success'] ? COLOR_GREEN . 'Yes' : COLOR_RED . 'No') . COLOR_RESET . PHP_EOL;
    echo "  Message: " . COLOR_CYAN . $result['message'] . COLOR_RESET . PHP_EOL;
    echo PHP_EOL;

    if ($result['success']) {
        printColor("  OK: Test email sent successfully", COLOR_GREEN);
        printColor("  Please check your inbox at $testEmail", COLOR_GREEN);
    } else {
        printColor("  FAIL: Failed to send test email", COLOR_RED);
        printColor("  Check the logs at logs/email.log for more details", COLOR_YELLOW);
    }

    echo PHP_EOL;
    echo "  Log files to check:" . PHP_EOL;
    echo "    - logs/email.log (attempt logs)" . PHP_EOL;
    echo "    - logs/email_content.log (full content when in debug mode)" . PHP_EOL;
}

// ========================================
// MAIN SCRIPT
// ========================================

echo PHP_EOL;
echo COLOR_BOLD . COLOR_CYAN . "Resend Email Configuration Test & Debug Tool" . COLOR_RESET . PHP_EOL;

// Check for command line arguments
$sendTest = isset($argv[1]) && $argv[1] === 'send-test';
$testEmail = isset($argv[2]) ? $argv[2] : (getenv('TEST_EMAIL') ?: '');

printHeader("Environment Configuration");

// Check if running from CLI or web
$isCLI = php_sapi_name() === 'cli';
echo "  Running from: " . COLOR_CYAN . ($isCLI ? 'CLI (Command Line)' : 'Web Server') . COLOR_RESET . PHP_EOL;
echo "  PHP Version: " . COLOR_CYAN . PHP_VERSION . COLOR_RESET . PHP_EOL;
echo "  Base URL: " . COLOR_CYAN . BASE_URL . COLOR_RESET . PHP_EOL;
echo "  Site Name: " . COLOR_CYAN . SITE_NAME . COLOR_RESET . PHP_EOL;
echo PHP_EOL;

// Resend Configuration
printHeader("Resend Configuration");

$apiKey = EmailConfig::resendApiKey();
$apiBaseUrl = EmailConfig::resendApiBaseUrl();
$emailsUrl = EmailConfig::resendEmailsUrl();
$senderEmail = EmailConfig::senderEmail();
$senderName = EmailConfig::senderName();
$debugMode = EmailConfig::debugMode();

printConfig('API Key', $apiKey, true);
printConfig('API Base URL', $apiBaseUrl, false);
printConfig('Emails URL', $emailsUrl, false);
printConfig('From Email', $senderEmail, true);
printConfig('From Name', $senderName, false);
printConfig('Debug Mode', $debugMode ? 'ENABLED' : 'DISABLED', false);

echo PHP_EOL;

// Configuration Status
printHeader("Configuration Status");

$hasRequiredConfig = !empty($apiKey) && !empty($senderEmail);

if ($hasRequiredConfig) {
    printColor("  OK: All required Resend credentials are configured", COLOR_GREEN);
} else {
    printColor("  FAIL: Missing required Resend credentials", COLOR_RED);
    echo COLOR_YELLOW . "  WARN: Please configure the following environment variables:" . COLOR_RESET . PHP_EOL;
    if (empty($apiKey)) echo "    - RESEND_API_KEY" . PHP_EOL;
    if (empty($senderEmail)) echo "    - RESEND_FROM_EMAIL" . PHP_EOL;
}

// Check for debug mode
if ($debugMode) {
    printColor("  INFO: Debug mode is ENABLED - emails will be logged but not sent", COLOR_YELLOW);
}

// Check log files
echo PHP_EOL;
printHeader("Log Files");

$logFiles = [
    'Email Attempts' => __DIR__ . '/logs/email.log',
    'Email Content' => __DIR__ . '/logs/email_content.log'
];

foreach ($logFiles as $name => $path) {
    if (file_exists($path)) {
        $size = filesize($path);
        $sizeStr = $size > 1024 ? round($size / 1024, 2) . ' KB' : $size . ' bytes';
        $modified = date('Y-m-d H:i:s', filemtime($path));
        echo "  $name: " . COLOR_GREEN . "EXISTS" . COLOR_RESET . " (Size: $sizeStr, Modified: $modified)" . PHP_EOL;
        echo "           Path: $path" . PHP_EOL;
    } else {
        echo "  $name: " . COLOR_YELLOW . "NOT FOUND" . COLOR_RESET . " (Path: $path)" . PHP_EOL;
    }
}

// Test Resend connection
if ($hasRequiredConfig) {
    $connectionSuccess = testResendConnection($apiKey, $apiBaseUrl);
} else {
    printHeader("Connection Test");
    printColor("  WARN: Skipping connection test - missing required credentials", COLOR_YELLOW);
}

// Send test email if requested
if ($sendTest) {
    if (!$hasRequiredConfig) {
        printHeader("Test Email");
        printColor("  FAIL: Cannot send test email - missing required credentials", COLOR_RED);
    } elseif (empty($testEmail)) {
        printHeader("Test Email");
        printColor("  FAIL: No test email address provided", COLOR_RED);
        echo PHP_EOL;
        echo "  Usage: php test_email_config.php send-test <email@example.com>" . PHP_EOL;
        echo "  Or set TEST_EMAIL environment variable" . PHP_EOL;
    } else {
        sendTestEmail($testEmail);
    }
}

// Summary
printHeader("Summary");

if ($hasRequiredConfig) {
    printColor("  OK: Resend is configured and ready to use", COLOR_GREEN);
    if ($sendTest && isset($connectionSuccess) && $connectionSuccess) {
        printColor("  OK: Test completed successfully", COLOR_GREEN);
    }
} else {
    printColor("  FAIL: Resend needs configuration before use", COLOR_RED);
    echo PHP_EOL;
    echo "  Next steps:" . PHP_EOL;
    echo "    1. Sign up at https://resend.com" . PHP_EOL;
    echo "    2. Verify a sending domain or address" . PHP_EOL;
    echo "    3. Add the following to your .env file:" . PHP_EOL;
    echo "       RESEND_API_KEY=your_api_key" . PHP_EOL;
    echo "       RESEND_FROM_EMAIL=sender@yourdomain.com" . PHP_EOL;
    echo "       RESEND_FROM_NAME=Your Blog Name (optional)" . PHP_EOL;
    echo "    4. Run this script again to verify configuration" . PHP_EOL;
    echo "       php test_email_config.php send-test your@email.com" . PHP_EOL;
}

echo PHP_EOL;
echo COLOR_BLUE . str_repeat('=', 70) . COLOR_RESET . PHP_EOL;
echo PHP_EOL;
