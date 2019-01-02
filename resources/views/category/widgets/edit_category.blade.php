<form action="{{ route('categories.update', $category->id) }}" method="post">
    @method('PUT')
    @csrf

    @include('category.partials.edit')

    <button type="submit" class="btn btn-primary">
        Edit
    </button>
</form>
