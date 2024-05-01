@if(config('honeypot.enabled'))
    <div style="display: none" aria-hidden="true">
        <label for="{{ config('honeypot.field_name') }}">{{ ucfirst(config('honeypot.field_name')) }}</label>
        <input type="text" name="{{ config('honeypot.field_name') }}" value="">
    </div>
@endif
