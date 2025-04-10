@include('layouts.partials.errors')

<div class="mb-3">
    <label for="date" class="form-label">Date</label>
    <input type="date" class="form-control {{ $errors->has('date') ? ' is-invalid ' : '' }}" id="date" name="date" placeholder="Enter date" value="{{ old('date', $day->date) }}" required>
    @if($errors->has('date'))
        <div class="invalid-feedback">
            {{ $errors->first('date') }}
        </div>
    @endif
</div>

<div class="alert alert-warning d-none" role="alert" id="existing-date-alert">
    <div class="d-flex align-items-center">
        <div class="flex-fill">
            Heads up! There is already an entry for this date. <a href="" id="existing-date-link" class="alert-link">Visit the existing entry </a>.
        </div>
    </div>
</div>

<div class="mb-3">
    <div class="row">
    @foreach($categories as $category)
            <div class="col d-flex">
            <input type="radio" class="btn-check" autocomplete="off" name="category_id" id="category_{{ $loop->index }}" required
                   {{ old('category_id', $day->category_id) == $category->id ? ' checked ' : '' }} value="{{ $category->id }}">
                <label for="category_{{ $loop->index }}" style="font-size: xxx-large; flex:1; border-radius: .5rem;" class=" {{ $errors->has('category_id') ? ' is-invalid ' : '' }}
         p-2 mt-2 text-center btn btn-outline-primary
        ">{!! $category->name  !!}</label>
            </div>
    @endforeach
    </div>

    @if($errors->has('category_id'))
        <div class="invalid-feedback">
            {{ $errors->first('category_id') }}
        </div>
    @endif
</div>

<div class="mb-3">
    <label for="tag_ids" class="form-label">Tags</label>
    <ul class="list-group" id="tag_ids" >
        @foreach($tags as $tag)
        <li class="list-group-item {{ $errors->has('tag_ids') ? ' is-invalid ' : '' }}">
            <input class="form-check-input me-1" name="tag_ids[]" type="checkbox" value="{{ $tag->slug }}" id="tag-{{ $tag->slug }}" {{ in_array($tag->slug, old('tag_ids', $selectedTags)) ? ' checked ' : '' }}>
            <label class="form-check-label stretched-link" for="tag-{{ $tag->slug }}">{{ $tag->name }}</label>
        </li>
        @endforeach
        <li class="list-group-item" id="tag-form-item">
            <div class="input-group" id="tag-form-input-group">
                <input type="text" class="form-control" id="tag-form-input" placeholder="Enter tag name">
                <label for="tag-form-input" class="visually-hidden">Tag Name</label>
                <button class="btn btn-outline-secondary" type="button" id="tag-form-button">Create tag</button>
            </div>
            <div class="invalid-feedback" id="tag-invalid-feedback">
            </div>
        </li>
    </ul>
    @if($errors->has('tag_ids'))
        <div class="invalid-feedback">
            {{ $errors->first('tag_ids') }}
        </div>
    @endif
</div>

<div class="mb-3">
    <label for="comment" class="form-label">Comment</label>
    <textarea class="form-control {{ $errors->has('comment') ? ' is-invalid ' : '' }}" id="comment" name="comment" placeholder="Enter comment">{{ old('comment', $day->comment) }}</textarea>
    @if($errors->has('comment'))
        <div class="invalid-feedback">
            {{ $errors->first('comment') }}
        </div>
    @endif
</div>

<div class="mb-3">
    <label for="grateful_for" class="form-label">I am grateful for</label>
    <textarea class="form-control {{ $errors->has('grateful_for') ? ' is-invalid ' : '' }}" id="grateful_for" name="grateful_for" placeholder="Today I am grateful for..."  maxlength="255"
    >{{ old('grateful_for', $day->grateful_for) }}</textarea>
    <div id="grateful_for-Help" class="form-text">Characters remaining: <span id="grateful_for-Remaining"></span></div>
    @if($errors->has('grateful_for'))
        <div class="invalid-feedback">
            {{ $errors->first('grateful_for') }}
        </div>
    @endif
</div>
