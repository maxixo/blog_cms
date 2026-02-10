<?php
require_once __DIR__ . '/../config/BrevoConfig.php';

class BrevoEmailService
{
    private $apiKey;
    private $apiUrl;
    private $senderEmail;
    private $senderName;
    private $debugMode;
    
    public function __construct() {
        $this->apiKey = BREVO_API_KEY;
        $this->apiUrl = 'https://api.brevo.com/v3/smtp/email';
        $this->senderEmail = BREVO_SENDER_EMAIL;
        $this->senderName = BREVO_SENDER_NAME;
        $this->debugMode = BREVO_DEBUG_MODE;
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
        $verificationUrl = EMAIL_VERIFICATION_URL . '?token=' . $token;
        $subject = 'Verify Your Email Address - ' . SITE_NAME;
        
        $template = $this->loadEmailTemplate('email-verification', [
            'username' => $username,
            'verification_url' => $verificationUrl,
            'expiry_hours' => floor(EMAIL_VERIFICATION_TOKEN_EXPIRY / 3600)
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
        $resetUrl = PASSWORD_RESET_URL . '?token=' . $token;
        $subject = 'Reset Your Password - ' . SITE_NAME;
        
        $template = $this->loadEmailTemplate('password-reset', [
            'username' => $username,
            'reset_url' => $resetUrl,
            'expiry_minutes' => floor(PASSWORD_RESET_TOKEN_EXPIRY / 60)
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
     * Generic email sending method using Brevo HTTP API
     * 
     * @param string $toEmail Recipient email
     * @param string $toName Recipient name
     * @param string $subject Email subject
     * @param string $htmlContent HTML content
     * @return array Result with success status and message
     */
    private function sendEmail($toEmail, $toName, $subject, $htmlContent): array
    {
        // Validate email
        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            $this->logEmailAttempt('error', $toEmail, false, 'Invalid email address');
            return [
                'success' => false,
                'message' => 'Invalid email address'
            ];
        }
        
        // Check API key
        if (empty($this->apiKey)) {
            $this->logEmailAttempt('error', $toEmail, false, 'Brevo API key not configured');
            return [
                'success' => false,
                'message' => 'Email service not configured'
            ];
        }
        
        // Prepare email data according to Brevo API v3
        $data = [
            'sender' => [
                'name' => $this->senderName,
                'email' => $this->senderEmail
            ],
            'to' => [
                [
                    'email' => $toEmail,
                    'name' => $toName
                ]
            ],
            'subject' => $subject,
            'htmlContent' => $htmlContent
        ];
        
        // If in debug mode, log instead of sending
        if ($this->debugMode) {
            $this->logEmailAttempt('debug', $toEmail, true, 'Email would be sent (debug mode)');
            $this->logEmailContent($toEmail, $subject, $htmlContent);
            return [
                'success' => true,
                'message' => 'Email logged in debug mode'
            ];
        }
        
        // Send email via cURL
        $jsonData = json_encode($data);
        
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'api-key: ' . $this->apiKey,
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        // Check for cURL errors
        if ($curlError) {
            $this->logEmailAttempt('error', $toEmail, false, 'cURL error: ' . $curlError);
            return [
                'success' => false,
                'message' => 'Failed to send email. Please try again later.'
            ];
        }
        
        // Check HTTP response code
        if ($httpCode >= 200 && $httpCode < 300) {
            $responseData = json_decode($response, true);
            $messageId = $responseData['messageId'] ?? '';
            
            $this->logEmailAttempt('success', $toEmail, true, 'Email sent successfully (ID: ' . $messageId . ')');
            return [
                'success' => true,
                'message' => 'Email sent successfully'
            ];
        } else {
            $responseData = json_decode($response, true);
            $errorMessage = 'Unknown error';
            
            if (isset($responseData['message'])) {
                $errorMessage = $responseData['message'];
            } elseif (isset($responseData['error']) && is_array($responseData['error'])) {
                $errorMessage = is_string($responseData['error']) 
                    ? $responseData['error'] 
                    : implode(', ', $responseData['error']);
            }
            
            $this->logEmailAttempt('error', $toEmail, false, "HTTP $httpCode: $errorMessage");
            
            // Return user-friendly message
            $userMessage = 'Failed to send email. Please try again later.';
            if (strpos($errorMessage, 'sender') !== false) {
                $userMessage = 'Email sender not configured. Please verify sender in Brevo settings.';
            } elseif (strpos($errorMessage, 'invalid') !== false) {
                $userMessage = 'Invalid email address.';
            }
            
            return [
                'success' => false,
                'message' => $userMessage
            ];
        }
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
        
        // Start output buffering
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
        
        // Create logs directory if it doesn't exist
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        // Append to log file
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
        
        // Create logs directory if it doesn't exist
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        // Append to log file
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
}