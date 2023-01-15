<form action="{{ route('tags.update', $tag) }}" method="post">
    @method('PUT')
    @csrf

    @include('tag.partials.edit')

    <button type="submit" class="btn btn-primary">
        Edit
    </button>
</form>
