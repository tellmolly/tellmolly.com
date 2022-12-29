<x-mail::message>
# Account Inactive

We're just letting you know that there has been no activity in the last {{ $inactive }} days.

Your account is automatically going to be deleted if you do not sign in within the next {{ $remaining }} days.

@if (config('calendar.actions.reset'))
If you forgot your password you can [request a new password]({{ route('password.request') }}).
@endif

<x-mail::button :url="route('welcome')">
Visit Tell Molly
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
