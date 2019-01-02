<form action="{{ route('categories.store') }}" method="post">
    @method('POST')
    @csrf

    @include('category.partials.edit')

    <button type="submit" class="btn btn-primary">
        Create
    </button>
</form>
