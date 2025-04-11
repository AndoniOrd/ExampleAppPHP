<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $subject ?? 'Campaign Email' }}</title>
</head>
<body>
    <h1>{{ $campaign_name ?? 'Our Campaign' }}</h1>
    
    <p>Hello {{ $contact_name ?? 'there' }},</p>
    
    <div>
        <!-- Your email content goes here -->
        <p>Thank you for subscribing to our newsletter.</p>
        
        <!-- Properly structured conditional for unsubscribe link -->
        @if(isset($unsubscribe_link) && $unsubscribe_link)
            <p>
                <a href="{{ $unsubscribe_link }}">Unsubscribe</a> from this mailing list.
            </p>
        @endif
        
        <!-- No extra endifs here -->
    </div>
    
    <footer>
        <p>© {{ date('Y') }} Your Company. All rights reserved.</p>
    </footer>
</body>
</html>