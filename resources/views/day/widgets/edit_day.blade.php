<form action="{{ route('days.update', $day->id) }}" method="post">
    @method('PUT')
    @csrf

    @include('day.partials.edit')

    <button type="submit" class="btn btn-primary">
        Edit
    </button>
</form>
