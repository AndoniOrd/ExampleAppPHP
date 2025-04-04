@component('mail::message')
# Import Error Report

The system has detected errors during the email contacts import process.

## Error Summary
@if(count($errors['existing'] ?? []) > 0)
* {{ count($errors['existing']) }} existing email(s) were found
@endif

@if(count($errors['invalid'] ?? []) > 0)
* {{ count($errors['invalid']) }} invalid email(s) were found
@endif

@if(count($errors['empty'] ?? []) > 0)
* {{ count($errors['empty']) }} row(s) with missing required data
@endif

## Details

@if(count($errors['existing'] ?? []) > 0)
### Existing Emails
@php $showCount = min(count($errors['existing']), 10); @endphp
@foreach(array_slice($errors['existing'], 0, $showCount) as $error)
- {{ $error['email'] }} (Row {{ $error['row'] }})
@endforeach
@if(count($errors['existing']) > 10)
... and {{ count($errors['existing']) - 10 }} more
@endif
@endif

@if(count($errors['invalid'] ?? []) > 0)
### Invalid Emails
@php $showCount = min(count($errors['invalid']), 10); @endphp
@foreach(array_slice($errors['invalid'], 0, $showCount) as $error)
- {{ $error['email'] }} (Row {{ $error['row'] }}): {{ $error['reason'] }}
@endforeach
@if(count($errors['invalid']) > 10)
... and {{ count($errors['invalid']) - 10 }} more
@endif
@endif

@if(count($errors['empty'] ?? []) > 0)
### Missing Data
@php $showCount = min(count($errors['empty']), 10); @endphp
@foreach(array_slice($errors['empty'], 0, $showCount) as $error)
- Row {{ $error['row'] }}: {{ $error['reason'] }}
@endforeach
@if(count($errors['empty']) > 10)
... and {{ count($errors['empty']) - 10 }} more
@endif
@endif

**Total errors:** {{ count($errors['existing'] ?? []) + count($errors['invalid'] ?? []) + count($errors['empty'] ?? []) }}  
**Import time:** {{ $timestamp }}

@component('mail::button', ['url' => url('/admin/import-history')])
View Import History
@endcomponent

This is an automated message. Please do not reply to this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent