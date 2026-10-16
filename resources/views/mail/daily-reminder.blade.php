<x-mail::message>
# How was your day?

Take a moment to note how today went and what you are grateful for.

<x-mail::button :url="route('days.today')">
Log today
</x-mail::button>

You are getting this email because you turned on daily reminders. You can change the time in your [profile]({{ route('profile.edit') }}) or [turn reminders off]({{ $unsubscribeUrl }}).

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
