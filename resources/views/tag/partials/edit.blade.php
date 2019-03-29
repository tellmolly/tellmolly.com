<div class="form-group">
    <label for="name">Name</label>
    <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid ' : '' }}" id="name" name="name" placeholder="Enter tag name" value="{{ old('name', $tag->name) }}" required>
    @if($errors->has('name'))
        <div class="invalid-feedback">
            {{ $errors->first('name') }}
        </div>
    @endif
</div>

<div class="form-group">
    <label for="color">Color</label>
    <input type="color" class="form-control {{ $errors->has('color') ? ' is-invalid ' : '' }}" id="color" name="color" placeholder="Enter tag background color" value="{{ old('color', $tag->color) }}" required>
    @if($errors->has('color'))
        <div class="invalid-feedback">
            {{ $errors->first('color') }}
        </div>
    @endif
</div>

