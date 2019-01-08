<div class="col-md-4 mb-3">
    <div class="list-group">
        <a href="{{ route('home') }}" class="list-group-item list-group-item-action {{ active('home') }}">
            Home
        </a>
        <a href="{{ route('days.create') }}" class="list-group-item list-group-item-action {{ active('days.create') }}">
            Create day
        </a>
        <a href="{{ route('days.index') }}" class="list-group-item list-group-item-action {{ active('days.index') }}">
            List days
        </a>
        <a href="{{ route('categories.create') }}" class="list-group-item list-group-item-action {{ active('categories.create') }}">
            Create category
        </a>
        <a href="{{ route('categories.index') }}" class="list-group-item list-group-item-action {{ active('categories.index') }}">
            List categories
        </a>
    </div>
</div>
