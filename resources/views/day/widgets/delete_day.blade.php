<form action="{{ route('days.destroy', $day->id) }}" method="post">
    @method('DELETE')
    @csrf

    <button type="submit" class="btn btn-danger">
        Delete
    </button>
</form>
