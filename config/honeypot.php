<?php

return [
    'enabled' => env('HONEYPOT_ENABLED', true),
    // A hidden field that people never see, so only bots fill it in.
    'field_name' => 'username',
    // When the form was shown, encrypted so it can't be faked. Forms checked
    // with "honeypot:timed" must not be sent back sooner than this.
    'time_field_name' => 'form_shown_at',
    'minimum_seconds' => 3,
];
