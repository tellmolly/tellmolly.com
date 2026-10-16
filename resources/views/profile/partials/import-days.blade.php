<p>Import a Tell Molly export (.xlsx) into your account. Days you already have are kept as they are; only new days are added.</p>

<form method="post" action="{{ route('import') }}" enctype="multipart/form-data">
    @csrf

    @if (session('import'))
        <div class="alert alert-success" role="alert">
            {{ session('import') }}
        </div>
    @endif

    <div class="mb-3">
        <label for="import_file" class="form-label">Export file</label>
        <input type="file" class="form-control {{ $errors->import->any() ? ' is-invalid ' : '' }}" id="import_file" name="file" accept=".xlsx" required>
        @if($errors->import->any())
            <div class="invalid-feedback">
                <ul class="mb-0 ps-3">
                    @foreach($errors->import->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <button type="submit" class="btn btn-primary">
        Import data
    </button>
</form>
