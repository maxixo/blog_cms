<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Verify Your Email</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
        }
        .button:hover {
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
            transform: translateY(-2px);
        }
        .expiry {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
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
            color: #667eea;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Verify Your Email Address</h1>
        </div>
        <div class="content">
            <p class="greeting">Hi <?php echo htmlspecialchars($username ?? 'User'); ?>,</p>
            <p class="message">
                Thank you for registering! We're excited to have you on board. To complete your registration and unlock all features of your account, please verify your email address by clicking the button below.
            </p>
            
            <div class="button-container">
                <a href="<?php echo htmlspecialchars($verification_url); ?>" class="button">Verify Email Address</a>
            </div>
            
            <p class="message" style="text-align: center;">
                or copy and paste this link into your browser:<br>
                <span style="word-break: break-all; color: #667eea; font-size: 13px;">
                    <?php echo htmlspecialchars($verification_url); ?>
                </span>
            </p>
            
            <div class="expiry">
                <strong>⏰ Note:</strong> This verification link will expire in <?php echo (int)($expiry_hours ?? 24); ?> hours for your security.
            </div>
            
            <div class="warning">
                <strong>🔒 Security Alert:</strong> If you didn't create an account, you can safely ignore this email. Your information won't be used.
            </div>
        </div>
        <div class="footer">
            <p>
                If you're having trouble clicking the button, you can also visit our 
                <a href="<?php echo htmlspecialchars(BASE_URL ?? '#'); ?>">website</a> and log in to request a new verification email.
            </p>
            <p style="margin-top: 20px;">
                © <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME ?? 'Our Blog'); ?>. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>