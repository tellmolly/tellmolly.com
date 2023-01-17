<p>Your data belongs to you, which is why you can export all of your tags and days. </p>

<form method="post" action="{{ route('export') }}">
    @csrf

    <button type="submit" class="btn btn-primary">
        Export data
    </button>
</form>
