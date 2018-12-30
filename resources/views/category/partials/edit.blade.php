<div class="form-group">
    <label for="name">Name</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="Enter category name" value="{{ old('name', $category->name) }}" required>
    @if($errors->has('name'))
        <div class="invalid-feedback">
            {{ $errors->first('name') }}
        </div>
    @endif
</div>

<div class="form-group">
    <label for="color">Color</label>
    <input type="color" class="form-control" id="color" name="color" placeholder="Enter category background color" value="{{ old('color', $category->color) }}" required>
    @if($errors->has('color'))
        <div class="invalid-feedback">
            {{ $errors->first('color') }}
        </div>
    @endif
</div>

