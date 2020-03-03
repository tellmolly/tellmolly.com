<div class="col-md-4 mb-3">
    <div class="list-group">
        <a href="{{ route('home') }}" class="list-group-item list-group-item-action {{ request()->route()->named('home') ? 'active' : '' }}">
            Home
        </a>
        <a href="{{ route('days.create') }}" class="list-group-item list-group-item-action {{ request()->route()->named('days.create') ? 'active' : '' }}">
            Create day
        </a>
        <a href="{{ route('days.index') }}" class="list-group-item list-group-item-action {{ request()->route()->named('days.index') ? 'active' : '' }}">
            List days
        </a>
        <a href="{{ route('categories.create') }}" class="list-group-item list-group-item-action {{ request()->route()->named('categories.create') ? 'active' : '' }}">
            Create category
        </a>
        <a href="{{ route('categories.index') }}" class="list-group-item list-group-item-action {{ request()->route()->named('categories.index') ? 'active' : '' }}">
            List categories
        </a>
        <a href="{{ route('tags.create') }}" class="list-group-item list-group-item-action {{ request()->route()->named('tags.create') ? 'active' : '' }}">
            Create tag
        </a>
        <a href="{{ route('tags.index') }}" class="list-group-item list-group-item-action {{ request()->route()->named('tags.index') ? 'active' : '' }}">
            List tags
        </a>
    </div>
</div>
