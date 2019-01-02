<form action="{{ route('days.store') }}" method="post">
    @method('POST')
    @csrf

    @include('day.partials.edit')

    <button type="submit" class="btn btn-primary">
        Create
    </button>
</form>
