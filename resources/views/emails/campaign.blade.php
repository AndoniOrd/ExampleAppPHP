{{-- resources/views/emails/campaign.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>{{ $campaign_name }} Campaign</title>
</head>
<body>
    <h1>Hello {{ $contact_name }}!</h1>
    
    <p>This is a test email for the campaign: {{ $campaign_name }}</p>
    
    <p>If you wish to unsubscribe, please click here.</p>
</body>
</html>