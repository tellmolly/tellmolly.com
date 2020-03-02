<form action="{{ route('categories.destroy', $category->id) }}" method="post" class="confirm">
    @method('DELETE')
    @csrf

    <button type="submit" class="btn btn-danger">
        Delete
    </button>
</form>
