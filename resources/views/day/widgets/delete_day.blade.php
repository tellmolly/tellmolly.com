<form action="{{ route('days.destroy', $day) }}" method="post" class="confirm">
    @method('DELETE')
    @csrf

    <button type="submit" class="btn btn-danger">
        Delete
    </button>
</form>
