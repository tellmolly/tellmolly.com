<form action="{{ route('categories.destroy', $category->id) }}" method="post">
    @method('DELETE')
    @csrf

    <button type="submit" class="btn btn-danger">
        Delete
    </button>
</form>
