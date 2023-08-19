<form action="{{ route('tags.update', $tag) }}" method="post">
    @method('PUT')
    @csrf

    @include('tag.partials.edit')

    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" name="archive" id="flexCheckDefault" @checked(old('archived', $tag->archived_at != null))>
            <label class="form-check-label" for="flexCheckDefault">
                Archive
            </label>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        Edit
    </button>
</form>
