<div class="mb-3">
    <label for="date">Date</label>
    <input type="date" class="form-control {{ $errors->has('date') ? ' is-invalid ' : '' }}" id="date" name="date" placeholder="Enter date" value="{{ old('date', $day->date) }}" required>
    @if($errors->has('date'))
        <div class="invalid-feedback">
            {{ $errors->first('date') }}
        </div>
    @endif
</div>

<div class="mb-3">
    <label for="category_id">Category</label>
    <select class="form-control {{ $errors->has('category_id') ? ' is-invalid ' : '' }}" id="category_id" name="category_id" required>
        <option value="">Choose category</option>
        @foreach($categories as $category)
            <option {{ old('category_id', $day->category_id) == $category->id ? ' selected ' : '' }} value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    </select>
    @if($errors->has('category_id'))
        <div class="invalid-feedback">
            {{ $errors->first('category_id') }}
        </div>
    @endif
</div>

<div class="mb-3">
    <label for="tag_ids">Tags</label>
    <select class="form-control {{ $errors->has('tag_ids') ? ' is-invalid ' : '' }}" id="tag_ids" name="tag_ids[]" multiple>
        @foreach($tags as $tag)
            <option {{ in_array($tag->id, old('tag_ids', $day->tags()->pluck("id")->values()->toArray())) ? ' selected ' : '' }} value="{{ $tag->id }}">{{ $tag->name }}</option>
        @endforeach
    </select>
    @if($errors->has('tag_ids'))
        <div class="invalid-feedback">
            {{ $errors->first('tag_ids') }}
        </div>
    @endif
</div>

<div class="mb-3">
    <label for="comment">Comment</label>
    <textarea class="form-control {{ $errors->has('comment') ? ' is-invalid ' : '' }}" id="comment" name="comment" placeholder="Enter comment">{{ old('comment', $day->comment) }}</textarea>
    @if($errors->has('comment'))
        <div class="invalid-feedback">
            {{ $errors->first('comment') }}
        </div>
    @endif
</div>
