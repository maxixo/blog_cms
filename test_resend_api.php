<?php
/**
 * Simple Resend API Test Script
 *
 * This script tests the Resend API configuration and sends a test email.
 * 
 * Usage:
 *   php test_resend_api.php                    - Check configuration and test connection
 *   php test_resend_api.php send <email>       - Send a test email to the specified address
 *
 * Example:
 *   php test_resend_api.php send user@example.com
 */

// Enable error reporting
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
 * Print colored text
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
 * Load environment variables from .env file
 */
function loadEnv($path)
{
    if (!file_exists($path)) {
        return false;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        if (strpos($line, '=') === false) {
            continue;
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
    
    return true;
}

// Load environment variables
loadEnv(__DIR__ . '/.env');

// Get configuration
$apiKey = getenv('RESEND_API_KEY') ?: '';
$apiUrl = getenv('RESEND_API_URL') ?: 'https://api.resend.com';
$fromEmail = getenv('RESEND_FROM_EMAIL') ?: '';
$fromName = getenv('RESEND_FROM_NAME') ?: 'BlogCMS';

// Display header
echo PHP_EOL;
echo COLOR_BOLD . COLOR_CYAN . "  Resend API Test Tool" . COLOR_RESET . PHP_EOL;
echo COLOR_CYAN . "  Testing email functionality with Resend API" . COLOR_RESET . PHP_EOL;

// Check configuration
printHeader("Configuration Check");

$configItems = [
    'API Key' => $apiKey,
    'API URL' => $apiUrl,
    'From Email' => $fromEmail,
    'From Name' => $fromName
];

$hasRequiredConfig = true;
foreach ($configItems as $label => $value) {
    $isRequired = ($label === 'API Key' || $label === 'From Email');
    $status = !empty($value) ? 'OK' : 'MISSING';
    $statusColor = !empty($value) ? COLOR_GREEN : COLOR_RED;
    $valueDisplay = !empty($value) ? $value : '[NOT SET]';
    
    if ($isRequired && empty($value)) {
        $hasRequiredConfig = false;
    }
    
    printf("  %-20s %s %s%s\n", $label . ':', $statusColor . $status, COLOR_CYAN . $valueDisplay, COLOR_RESET);
}

if (!$hasRequiredConfig) {
    printColor("  ERROR: Missing required configuration!", COLOR_RED);
    echo PHP_EOL;
    echo "  Please add the following to your .env file:" . COLOR_CYAN . PHP_EOL;
    echo "    RESEND_API_KEY=your_api_key_here" . COLOR_RESET . PHP_EOL;
    echo "    RESEND_FROM_EMAIL=sender@yourdomain.com" . COLOR_RESET . PHP_EOL;
    echo PHP_EOL;
    exit(1);
}

printColor("  OK: All required configuration is present", COLOR_GREEN);

// Test API connection
printHeader("Testing API Connection");

$domainsUrl = rtrim($apiUrl, '/') . '/domains';
echo "  Testing connection to: $domainsUrl" . PHP_EOL;
echo PHP_EOL;

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

if ($curlError) {
    printColor("  FAIL: Connection error", COLOR_RED);
    echo "  cURL Error: $curlError" . PHP_EOL;
    echo PHP_EOL;
    echo "  Possible causes:" . PHP_EOL;
    echo "    - Network connection issue" . PHP_EOL;
    echo "    - Firewall blocking outbound connections" . PHP_EOL;
    echo "    - Invalid API URL" . PHP_EOL;
    exit(1);
}

echo "  HTTP Status: " . ($httpCode >= 200 && $httpCode < 300 ? COLOR_GREEN : COLOR_RED) . $httpCode . COLOR_RESET . PHP_EOL;

if ($httpCode >= 200 && $httpCode < 300) {
    $responseObj = json_decode($response, true);
    printColor("  OK: Connection successful!", COLOR_GREEN);
    
    if (!empty($responseObj['data'])) {
        echo PHP_EOL;
        echo "  Verified domains:" . COLOR_CYAN . PHP_EOL;
        foreach ($responseObj['data'] as $domain) {
            $verified = !empty($domain['verified_at']);
            $status = $verified ? COLOR_GREEN . '✓' : COLOR_RED . '✗';
            echo "    $status {$domain['name']} " . COLOR_RESET . PHP_EOL;
        }
    }
} else {
    printColor("  FAIL: API returned error", COLOR_RED);
    echo "  Response: " . COLOR_CYAN . $response . COLOR_RESET . PHP_EOL;
    echo PHP_EOL;
    echo "  Possible causes:" . PHP_EOL;
    echo "    - Invalid API key" . PHP_EOL;
    echo "    - API key expired or revoked" . PHP_EOL;
    echo "    - API service temporarily unavailable" . PHP_EOL;
}

echo PHP_EOL;

// Check if we should send a test email
$sendTest = isset($argv[1]) && $argv[1] === 'send';
$testEmail = isset($argv[2]) ? $argv[2] : '';

if ($sendTest) {
    if (empty($testEmail)) {
        printHeader("Send Test Email");
        printColor("  ERROR: No email address provided", COLOR_RED);
        echo PHP_EOL;
        echo "  Usage: php test_resend_api.php send <email@example.com>" . PHP_EOL;
        exit(1);
    }
    
    printHeader("Sending Test Email");
    echo "  To: " . COLOR_CYAN . $testEmail . COLOR_RESET . PHP_EOL;
    echo "  From: " . COLOR_CYAN . $fromName . " <$fromEmail>" . COLOR_RESET . PHP_EOL;
    echo PHP_EOL;
    
    $emailsUrl = rtrim($apiUrl, '/') . '/emails';
    
    $emailData = [
        'from' => "$fromName <$fromEmail>",
        'to' => [$testEmail],
        'subject' => 'Test Email from Resend API',
        'html' => '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
                <h1 style="color: #333;">Test Email Successful!</h1>
                <p style="color: #666; line-height: 1.6;">
                    This is a test email sent through the Resend API. If you received this, your email configuration is working correctly.
                </p>
                <div style="background: #f4f4f4; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 0; font-size: 14px; color: #666;">
                        <strong>Test Details:</strong><br>
                        Sent at: ' . date('Y-m-d H:i:s') . '<br>
                        API URL: ' . $apiUrl . '<br>
                        From: ' . $fromEmail . '
                    </p>
                </div>
                <p style="color: #666;">You can safely delete this email.</p>
            </div>
        '
    ];
    
    echo "  Sending email via Resend API..." . PHP_EOL;
    echo PHP_EOL;
    
    $ch = curl_init($emailsUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        printColor("  FAIL: Connection error", COLOR_RED);
        echo "  cURL Error: $curlError" . PHP_EOL;
        exit(1);
    }
    
    echo "  HTTP Status: " . ($httpCode >= 200 && $httpCode < 300 ? COLOR_GREEN : COLOR_RED) . $httpCode . COLOR_RESET . PHP_EOL;
    
    if ($httpCode >= 200 && $httpCode < 300) {
        $responseObj = json_decode($response, true);
        printColor("  OK: Email sent successfully!", COLOR_GREEN);
        
        if (!empty($responseObj['id'])) {
            echo "  Email ID: " . COLOR_CYAN . $responseObj['id'] . COLOR_RESET . PHP_EOL;
        }
        
        echo PHP_EOL;
        printColor("  Please check your inbox at $testEmail", COLOR_GREEN);
        echo COLOR_YELLOW . "  Note: It may take a few seconds for the email to arrive" . COLOR_RESET . PHP_EOL;
    } else {
        printColor("  FAIL: Failed to send email", COLOR_RED);
        echo "  Response: " . COLOR_CYAN . $response . COLOR_RESET . PHP_EOL;
        
        $responseObj = json_decode($response, true);
        if (!empty($responseObj['message'])) {
            echo PHP_EOL;
            echo "  Error Message: " . COLOR_RED . $responseObj['message'] . COLOR_RESET . PHP_EOL;
            echo PHP_EOL;
            echo "  Possible causes:" . PHP_EOL;
            echo "    - Sender domain not verified in Resend" . PHP_EOL;
            echo "    - Invalid sender email format" . PHP_EOL;
            echo "    - Recipient email address is invalid" . PHP_EOL;
            echo "    - API rate limit exceeded" . PHP_EOL;
        }
    }
} else {
    printHeader("Send Test Email");
    echo "  To send a test email, run:" . COLOR_CYAN . PHP_EOL;
    echo "  php test_resend_api.php send your@email.com" . COLOR_RESET . PHP_EOL;
}

// Summary
printHeader("Summary");

if ($hasRequiredConfig && $httpCode >= 200 && $httpCode < 300) {
    printColor("  ✓ Resend API is configured and working correctly", COLOR_GREEN);
    if ($sendTest) {
        printColor("  ✓ Test email sent successfully", COLOR_GREEN);
    }
} else {
    printColor("  ✗ Resend API configuration needs attention", COLOR_RED);
}

echo PHP_EOL;
echo COLOR_BLUE . str_repeat('=', 70) . COLOR_RESET . PHP_EOL;
echo PHP_EOL;