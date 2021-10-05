<form action="{{ route('tags.destroy', $tag->id) }}" method="post" class="confirm">
    @method('DELETE')
    @csrf

    <button type="submit" class="btn btn-danger">
        Delete
    </button>
</form>
