<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Welcome to <?php echo htmlspecialchars(SITE_NAME ?? 'Our Blog'); ?></title>
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
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            color: rgba(255,255,255,0.9);
            margin: 10px 0 0;
            font-size: 16px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .message {
            margin-bottom: 25px;
            color: #555;
            line-height: 1.7;
        }
        .welcome-box {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .welcome-icon {
            font-size: 56px;
            margin-bottom: 15px;
        }
        .welcome-text {
            color: #2c3e50;
            font-size: 20px;
            font-weight: 600;
        }
        .button-container {
            text-align: center;
            margin: 35px 0;
        }
        .button {
            display: inline-block;
            padding: 15px 45px;
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
        .features {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            gap: 15px;
        }
        .feature {
            flex: 1;
            text-align: center;
            padding: 20px 10px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .feature-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .feature-title {
            font-weight: 600;
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
        }
        .feature-desc {
            font-size: 12px;
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
            font-weight: 500;
        }
        .social-links {
            text-align: center;
            margin: 20px 0;
            padding: 20px 0;
            border-top: 1px solid #e0e0e0;
            border-bottom: 1px solid #e0e0e0;
        }
        .social-links p {
            margin: 0 0 10px;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to <?php echo htmlspecialchars(SITE_NAME ?? 'Our Blog'); ?>!</h1>
            <p>We're thrilled to have you on board 🎉</p>
        </div>
        <div class="content">
            <div class="welcome-box">
                <div class="welcome-icon">👋</div>
                <div class="welcome-text">Hello, <?php echo htmlspecialchars($username ?? 'Friend'); ?>!</div>
            </div>
            
            <p class="message">
                Thank you for joining our community! You've just taken the first step towards an amazing experience. We're excited to have you with us and can't wait to see what you'll create.
            </p>
            
            <p class="message">
                Your account is now set up and ready to go. Here are some things you can do right away:
            </p>
            
            <div class="features">
                <div class="feature">
                    <div class="feature-icon">✏️</div>
                    <div class="feature-title">Create Content</div>
                    <div class="feature-desc">Share your thoughts with the world</div>
                </div>
                <div class="feature">
                    <div class="feature-icon">💬</div>
                    <div class="feature-title">Engage</div>
                    <div class="feature-desc">Comment on posts you love</div>
                </div>
                <div class="feature">
                    <div class="feature-icon">🔔</div>
                    <div class="feature-title">Stay Updated</div>
                    <div class="feature-desc">Get notifications on new content</div>
                </div>
            </div>
            
            <div class="button-container">
                <a href="<?php echo htmlspecialchars($site_url ?? '#'); ?>" class="button">Start Exploring</a>
            </div>
            
            <p class="message">
                If you have any questions or need help getting started, feel free to reach out to our support team. We're here to help you every step of the way.
            </p>
            
            <p class="message">
                <strong>Here's what to expect next:</strong>
            </p>
            <ul style="color: #555; line-height: 1.8;">
                <li>Explore our latest articles and blog posts</li>
                <li>Complete your profile to personalize your experience</li>
                <li>Subscribe to topics that interest you</li>
                <li>Start engaging with our community</li>
            </ul>
            
            <div class="social-links">
                <p>Connect with us on social media:</p>
                <a href="#" style="margin: 0 10px; font-size: 24px;">📘</a>
                <a href="#" style="margin: 0 10px; font-size: 24px;">🐦</a>
                <a href="#" style="margin: 0 10px; font-size: 24px;">📷</a>
                <a href="#" style="margin: 0 10px; font-size: 24px;">💼</a>
            </div>
        </div>
        <div class="footer">
            <p>
                Ready to dive in? Visit our <a href="<?php echo htmlspecialchars($site_url ?? '#'); ?>">website</a> and start your journey!
            </p>
            <p style="margin-top: 20px;">
                Questions? Check out our <a href="<?php echo htmlspecialchars($site_url ?? '#'); ?>/help">FAQ</a> or <a href="<?php echo htmlspecialchars($site_url ?? '#'); ?>/contact">contact us</a>.
            </p>
            <p style="margin-top: 20px; font-size: 12px; color: #999;">
                You received this email because you created an account at <?php echo htmlspecialchars(SITE_NAME ?? 'Our Blog'); ?>.
            </p>
            <p style="margin-top: 20px;">
                © <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME ?? 'Our Blog'); ?>. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>