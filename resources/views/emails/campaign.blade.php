<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $campaign_name ?? 'Campaign' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
        }
        .content {
            padding: 20px;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $campaign_name ?? 'Campaign Newsletter' }}</h1>
    </div>
    
    <div class="content">
        <p>Hello {{ $contact_name ?? 'Subscriber' }},</p>
        
        <p>Thank you for subscribing to our newsletter. We're excited to share the latest updates with you.</p>
        
        <p>This is a sample campaign email template. You can customize this template to fit your campaign needs.</p>
        
        <p>Best regards,<br>The Team</p>
    </div>
    
    <div class="footer">
        <p>© {{ date('Y') }} Your Company. All rights reserved.</p>
        <p>
            <small>If you no longer wish to receive these emails, you can <a href="#">unsubscribe here</a>.</small>
        </p>
    </div>
</body>
</html>