<form action="{{ route('tags.store') }}" method="post">
    @method('POST')
    @csrf

    @include('tag.partials.edit')

    <button type="submit" class="btn btn-primary">
        Create
    </button>
</form>
