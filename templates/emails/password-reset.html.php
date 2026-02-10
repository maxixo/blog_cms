<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Reset Your Password</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f4f4f4;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .message {
            margin-bottom: 30px;
            color: #555;
        }
        .button-container {
            text-align: center;
            margin: 40px 0;
        }
        .button {
            display: inline-block;
            padding: 14px 40px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4);
            transition: all 0.3s ease;
        }
        .button:hover {
            box-shadow: 0 6px 20px rgba(245, 87, 108, 0.6);
            transform: translateY(-2px);
        }
        .expiry {
            background-color: #fff5f5;
            border-left: 4px solid #f5576c;
            padding: 15px 20px;
            margin: 30px 0;
            border-radius: 4px;
            font-size: 14px;
            color: #666;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            font-size: 14px;
            color: #777;
            border-top: 1px solid #e0e0e0;
        }
        .footer a {
            color: #f5576c;
            text-decoration: none;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px 20px;
            margin: 30px 0;
            border-radius: 4px;
            font-size: 14px;
            color: #856404;
        }
        .icon {
            font-size: 48px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Reset Your Password</h1>
        </div>
        <div class="content">
            <div class="icon">🔑</div>
            
            <p class="greeting">Hi <?php echo htmlspecialchars($username ?? 'User'); ?>,</p>
            <p class="message">
                We received a request to reset your password. If you made this request, click the button below to create a new password. This link will only work once and expires in <?php echo (int)($expiry_minutes ?? 60); ?> minutes.
            </p>
            
            <div class="button-container">
                <a href="<?php echo htmlspecialchars($reset_url); ?>" class="button">Reset Password</a>
            </div>
            
            <p class="message" style="text-align: center;">
                or copy and paste this link into your browser:<br>
                <span style="word-break: break-all; color: #f5576c; font-size: 13px;">
                    <?php echo htmlspecialchars($reset_url); ?>
                </span>
            </p>
            
            <div class="expiry">
                <strong>⏰ Security Note:</strong> This password reset link will expire in <?php echo (int)($expiry_minutes ?? 60); ?> minutes for your protection.
            </div>
            
            <div class="warning">
                <strong>🔒 Security Alert:</strong> If you didn't request a password reset, please ignore this email. Your password will remain unchanged, and your account is still secure.
            </div>
            
            <p class="message" style="font-size: 14px; margin-top: 30px;">
                For security reasons, we recommend choosing a strong password that includes a mix of letters, numbers, and special characters.
            </p>
        </div>
        <div class="footer">
            <p>
                If you're having trouble clicking the button, you can also visit our 
                <a href="<?php echo htmlspecialchars(BASE_URL ?? '#'); ?>">website</a> and use the "Forgot Password" link on the login page.
            </p>
            <p style="margin-top: 20px;">
                © <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME ?? 'Our Blog'); ?>. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>