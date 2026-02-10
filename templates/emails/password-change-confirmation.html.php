<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Password Changed Successfully</title>
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
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
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
        .success-box {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            margin: 30px 0;
        }
        .success-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .success-text {
            color: #155724;
            font-size: 16px;
            font-weight: 600;
        }
        .details {
            background-color: #f8f9fa;
            border-left: 4px solid #38ef7d;
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
            color: #11998e;
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
        .tips {
            background-color: #e8f5e9;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }
        .tips h3 {
            margin-top: 0;
            color: #2e7d32;
            font-size: 16px;
        }
        .tips ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .tips li {
            margin: 8px 0;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Password Changed Successfully</h1>
        </div>
        <div class="content">
            <p class="greeting">Hi <?php echo htmlspecialchars($username ?? 'User'); ?>,</p>
            
            <div class="success-box">
                <div class="success-icon">✅</div>
                <div class="success-text">Your password has been successfully changed!</div>
            </div>
            
            <p class="message">
                This email is to confirm that your account password was changed successfully on <?php echo htmlspecialchars($changed_at ?? date('F j, Y, g:i a')); ?>.
            </p>
            
            <div class="details">
                <strong>📅 Change Details:</strong><br>
                Time: <?php echo htmlspecialchars($changed_at ?? date('F j, Y, g:i a')); ?><br>
                Account: <?php echo htmlspecialchars($username ?? 'Your account'); ?>
            </div>
            
            <div class="tips">
                <h3>🔐 Security Tips for Your New Password:</h3>
                <ul>
                    <li>Keep your password secure and don't share it with anyone</li>
                    <li>Use a unique password for each of your accounts</li>
                    <li>Consider using a password manager to generate and store strong passwords</li>
                    <li>Enable two-factor authentication if available</li>
                    <li>Change your password periodically for added security</li>
                </ul>
            </div>
            
            <div class="warning">
                <strong>⚠️ Important:</strong> If you didn't change your password, please contact our support team immediately and secure your account.
            </div>
        </div>
        <div class="footer">
            <p>
                For your security, always log out when you're done using your account, especially on shared or public computers.
            </p>
            <p style="margin-top: 20px;">
                Need help? Visit our <a href="<?php echo htmlspecialchars(BASE_URL ?? '#'); ?>">help center</a> or contact support.
            </p>
            <p style="margin-top: 20px;">
                © <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME ?? 'Our Blog'); ?>. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>