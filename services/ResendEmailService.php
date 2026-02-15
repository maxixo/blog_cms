<?php
require_once __DIR__ . '/../config/EmailConfig.php';

class ResendEmailService
{
    private $apiKey;
    private $emailsUrl;
    private $senderEmail;
    private $senderName;
    private $debugMode;

    public function __construct()
    {
        $this->apiKey = EmailConfig::resendApiKey();
        $this->emailsUrl = EmailConfig::resendEmailsUrl();
        $this->senderEmail = EmailConfig::senderEmail();
        $this->senderName = EmailConfig::senderName();
        $this->debugMode = EmailConfig::debugMode();
    }

    /**
     * Send email verification email
     *
     * @param string $email User's email
     * @param string $username User's username
     * @param string $token Verification token
     * @return array Result with success status and message
     */
    public function sendVerificationEmail($email, $username, $token): array
    {
        $verificationUrl = EmailConfig::verificationUrl($token);
        $subject = 'Verify Your Email Address - ' . SITE_NAME;

        $template = $this->loadEmailTemplate('email-verification', [
            'username' => $username,
            'verification_url' => $verificationUrl,
            'expiry_hours' => floor(EmailConfig::emailVerificationExpirySeconds() / 3600)
        ]);

        return $this->sendEmail($email, $username, $subject, $template['html']);
    }

    /**
     * Send password reset email
     *
     * @param string $email User's email
     * @param string $username User's username
     * @param string $token Reset token
     * @return array Result with success status and message
     */
    public function sendPasswordResetEmail($email, $username, $token): array
    {
        $resetUrl = EmailConfig::passwordResetUrl($token);
        $subject = 'Reset Your Password - ' . SITE_NAME;

        $template = $this->loadEmailTemplate('password-reset', [
            'username' => $username,
            'reset_url' => $resetUrl,
            'expiry_minutes' => floor(EmailConfig::passwordResetExpirySeconds() / 60)
        ]);

        return $this->sendEmail($email, $username, $subject, $template['html']);
    }

    /**
     * Send password change confirmation email
     *
     * @param string $email User's email
     * @param string $username User's username
     * @return array Result with success status and message
     */
    public function sendPasswordChangeConfirmation($email, $username): array
    {
        $subject = 'Password Changed Successfully - ' . SITE_NAME;

        $template = $this->loadEmailTemplate('password-change-confirmation', [
            'username' => $username,
            'changed_at' => date('F j, Y, g:i a')
        ]);

        return $this->sendEmail($email, $username, $subject, $template['html']);
    }

    /**
     * Send welcome email after registration
     *
     * @param string $email User's email
     * @param string $username User's username
     * @return array Result with success status and message
     */
    public function sendWelcomeEmail($email, $username): array
    {
        $subject = 'Welcome to ' . SITE_NAME;

        $template = $this->loadEmailTemplate('welcome', [
            'username' => $username,
            'site_url' => BASE_URL
        ]);

        return $this->sendEmail($email, $username, $subject, $template['html']);
    }

    /**
     * Generic email sending method using Resend HTTP API
     *
     * @param string $toEmail Recipient email
     * @param string $toName Recipient name
     * @param string $subject Email subject
     * @param string $htmlContent HTML content
     * @return array Result with success status and message
     */
    private function sendEmail($toEmail, $toName, $subject, $htmlContent): array
    {
        // Console debug output when debug mode is enabled
        if ($this->debugMode && php_sapi_name() === 'cli') {
            echo PHP_EOL . "\033[36m[EMAIL DEBUG]\033[0m Starting email send process..." . PHP_EOL;
            echo "\033[36m[EMAIL DEBUG]\033[0m To: $toName <$toEmail>" . PHP_EOL;
            echo "\033[36m[EMAIL DEBUG]\033[0m Subject: $subject" . PHP_EOL;
        }

        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            if ($this->debugMode && php_sapi_name() === 'cli') {
                echo "\033[31m[EMAIL ERROR]\033[0m Invalid email address: $toEmail" . PHP_EOL;
            }
            $this->logEmailAttempt('error', $toEmail, false, 'Invalid email address');
            return [
                'success' => false,
                'message' => 'Invalid email address'
            ];
        }

        if (empty($this->apiKey)) {
            if ($this->debugMode && php_sapi_name() === 'cli') {
                echo "\033[31m[EMAIL ERROR]\033[0m Resend API key not configured." . PHP_EOL;
            }
            $this->logEmailAttempt('error', $toEmail, false, 'Resend API key not configured');
            return [
                'success' => false,
                'message' => 'Email service not configured'
            ];
        }

        if (empty($this->senderEmail)) {
            if ($this->debugMode && php_sapi_name() === 'cli') {
                echo "\033[31m[EMAIL ERROR]\033[0m Sender email not configured." . PHP_EOL;
            }
            $this->logEmailAttempt('error', $toEmail, false, 'Sender email not configured');
            return [
                'success' => false,
                'message' => 'Email sender not configured'
            ];
        }

        $fromAddress = $this->senderEmail;
        if (!empty($this->senderName)) {
            $fromAddress = $this->senderName . ' <' . $this->senderEmail . '>';
        }

        $data = [
            'from' => $fromAddress,
            'to' => [$toEmail],
            'subject' => $subject,
            'html' => $htmlContent
        ];

        if ($this->debugMode) {
            if (php_sapi_name() === 'cli') {
                echo "\033[33m[EMAIL DEBUG]\033[0m Debug mode is ENABLED - email will be logged but NOT sent" . PHP_EOL;
            }
            $this->logEmailAttempt('debug', $toEmail, true, 'Email would be sent (debug mode)');
            $this->logEmailContent($toEmail, $subject, $htmlContent);
            return [
                'success' => true,
                'message' => 'Email logged in debug mode'
            ];
        }

        if (!function_exists('curl_init')) {
            $this->logEmailAttempt('error', $toEmail, false, 'cURL extension not enabled');
            return [
                'success' => false,
                'message' => 'Email service unavailable. Please contact the administrator.'
            ];
        }

        $jsonData = json_encode($data);

        if ($this->debugMode && php_sapi_name() === 'cli') {
            echo "\033[36m[EMAIL DEBUG]\033[0m Sending request to Resend API..." . PHP_EOL;
            echo "\033[36m[EMAIL DEBUG]\033[0m API URL: $this->emailsUrl" . PHP_EOL;
            echo "\033[36m[EMAIL DEBUG]\033[0m Request payload:" . PHP_EOL;
            echo json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL;
        }

        $ch = curl_init($this->emailsUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            if ($this->debugMode && php_sapi_name() === 'cli') {
                echo "\033[31m[EMAIL ERROR]\033[0m cURL error: $curlError" . PHP_EOL;
            }
            $this->logEmailAttempt('error', $toEmail, false, 'cURL error: ' . $curlError);
            return [
                'success' => false,
                'message' => 'Failed to send email. Please try again later. cURL error: ' . $curlError
            ];
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            if ($this->debugMode && php_sapi_name() === 'cli') {
                echo "\033[32m[EMAIL SUCCESS]\033[0m Email sent successfully!" . PHP_EOL;
                echo "\033[32m[EMAIL SUCCESS]\033[0m HTTP Code: $httpCode" . PHP_EOL;
                if (!empty($response)) {
                    echo "\033[32m[EMAIL SUCCESS]\033[0m Response: $response" . PHP_EOL;
                }
            }
            $this->logEmailAttempt('success', $toEmail, true, 'Email sent successfully');
            return [
                'success' => true,
                'message' => 'Email sent successfully'
            ];
        }

        if ($this->debugMode && php_sapi_name() === 'cli') {
            echo "\033[31m[EMAIL ERROR]\033[0m Failed to send email" . PHP_EOL;
            echo "\033[31m[EMAIL ERROR]\033[0m HTTP Code: $httpCode" . PHP_EOL;
            echo "\033[31m[EMAIL ERROR]\033[0m Response: $response" . PHP_EOL;
        }

        $errorMessage = 'Unknown error';
        if (is_string($response) && trim($response) !== '') {
            $decoded = json_decode($response, true);
            if (is_array($decoded)) {
                if (!empty($decoded['message'])) {
                    $errorMessage = $decoded['message'];
                } elseif (!empty($decoded['error'])) {
                    if (is_array($decoded['error']) && !empty($decoded['error']['message'])) {
                        $errorMessage = $decoded['error']['message'];
                    } else {
                        $errorMessage = is_array($decoded['error'])
                            ? implode(', ', $decoded['error'])
                            : (string) $decoded['error'];
                    }
                } else {
                    $errorMessage = trim($response);
                }
            } else {
                $errorMessage = trim($response);
            }
        }

        $this->logEmailAttempt('error', $toEmail, false, "HTTP $httpCode: $errorMessage");

        $userMessage = 'Failed to send email. Please try again later.';
        $errorLower = strtolower($errorMessage);
        if (strpos($errorLower, 'from') !== false || strpos($errorLower, 'domain') !== false) {
            $userMessage = 'Email sender not configured. Please verify your Resend sender.';
        } elseif (strpos($errorLower, 'authorization') !== false || strpos($errorLower, 'api key') !== false) {
            $userMessage = 'Email service not configured. Please verify your Resend API key.';
        }

        return [
            'success' => false,
            'message' => $userMessage
        ];
    }

    /**
     * Load email template and replace placeholders
     *
     * @param string $templateName Template name (without extension)
     * @param array $variables Variables to replace in template
     * @return array Array with 'html' content
     */
    private function loadEmailTemplate($templateName, $variables = []): array
    {
        $templatePath = __DIR__ . '/../templates/emails/' . $templateName . '.html.php';

        if (!file_exists($templatePath)) {
            return [
                'html' => 'Email template not found: ' . $templateName
            ];
        }

        ob_start();
        extract($variables);
        include $templatePath;
        $htmlContent = ob_get_clean();

        return [
            'html' => $htmlContent
        ];
    }

    /**
     * Log email send attempts
     *
     * @param string $type Type of log (success, error, debug)
     * @param string $email Recipient email
     * @param bool $success Whether the operation was successful
     * @param string $message Log message
     */
    private function logEmailAttempt($type, $email, $success, $message = '')
    {
        $logEntry = date('Y-m-d H:i:s') . " | Type: $type | Email: $email | Success: " . ($success ? 'yes' : 'no') . " | Message: $message" . PHP_EOL;

        $logFile = __DIR__ . '/../logs/email.log';
        $logDir = dirname($logFile);

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }

    /**
     * Log email content in debug mode
     *
     * @param string $email Recipient email
     * @param string $subject Email subject
     * @param string $content Email HTML content
     */
    private function logEmailContent($email, $subject, $content)
    {
        $logEntry = "\n" . str_repeat('=', 80) . "\n";
        $logEntry .= "Email Content - " . date('Y-m-d H:i:s') . "\n";
        $logEntry .= "To: $email\n";
        $logEntry .= "Subject: $subject\n";
        $logEntry .= str_repeat('=', 80) . "\n";
        $logEntry .= $content . "\n";
        $logEntry .= str_repeat('=', 80) . "\n\n";

        $logFile = __DIR__ . '/../logs/email_content.log';
        $logDir = dirname($logFile);

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
}
