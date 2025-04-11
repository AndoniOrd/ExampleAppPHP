IMPORT ERROR REPORT

The system has detected errors during the email contacts import process.

ERROR SUMMARY:
@if(count($errors['existing'] ?? []) > 0)
- {{ count($errors['existing']) }} existing email(s) were found
@endif
@if(count($errors['invalid'] ?? []) > 0)
- {{ count($errors['invalid']) }} invalid email(s) were found
@endif
@if(count($errors['empty'] ?? []) > 0)
- {{ count($errors['empty']) }} row(s) with missing required data
@endif

DETAILS:
@if(count($errors['existing'] ?? []) > 0)
Existing Emails:
@php $showCount = min(count($errors['existing']), 5); @endphp
@foreach(array_slice($errors['existing'], 0, $showCount) as $error)
* {{ $error['email'] }} (Row {{ $error['row'] }})
@endforeach
@if(count($errors['existing']) > 5)
... and {{ count($errors['existing']) - 5 }} more
@endif

@endif
@if(count($errors['invalid'] ?? []) > 0)
Invalid Emails:
@php $showCount = min(count($errors['invalid']), 5); @endphp
@foreach(array_slice($errors['invalid'], 0, $showCount) as $error)
* {{ $error['email'] }} (Row {{ $error['row'] }}): {{ $error['reason'] }}
@endforeach
@if(count($errors['invalid']) > 5)
... and {{ count($errors['invalid']) - 5 }} more
@endif

@endif
@if(count($errors['empty'] ?? []) > 0)
Missing Data:
@php $showCount = min(count($errors['empty']), 5); @endphp
@foreach(array_slice($errors['empty'], 0, $showCount) as $error)
* Row {{ $error['row'] }}: {{ $error['reason'] }}
@endforeach
@if(count($errors['empty']) > 5)
... and {{ count($errors['empty']) - 5 }} more
@endif

@endif
Total errors: {{ count($errors['existing'] ?? []) + count($errors['invalid'] ?? []) + count($errors['empty'] ?? []) }}
Import time: {{ $timestamp }}

View Import History: {{ url('/admin/import-history') }}

This is an automated message. Please do not reply to this email.

Thanks,
{{ config('app.name') }}