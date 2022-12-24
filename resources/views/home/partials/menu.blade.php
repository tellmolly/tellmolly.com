<div class="col-md-4 mb-3">
    <div class="list-group">
        <a href="{{ route('statistic.index') }}" class="list-group-item list-group-item-action {{ request()->route()->named('statistic.index') ? 'active' : '' }}">
            Dashboard
        </a>
        <a href="{{ route('days.create') }}" class="list-group-item list-group-item-action {{ request()->route()->named('days.create') ? 'active' : '' }}">
            New day
        </a>
        <a href="{{ route('days.index') }}" class="list-group-item list-group-item-action {{ request()->route()->named('days.index') ? 'active' : '' }}">
            List days
        </a>
        <a href="{{ route('tags.create') }}" class="list-group-item list-group-item-action {{ request()->route()->named('tags.create') ? 'active' : '' }}">
            New tag
        </a>
        <a href="{{ route('tags.index') }}" class="list-group-item list-group-item-action {{ request()->route()->named('tags.index') ? 'active' : '' }}">
            List tags
        </a>
    </div>
</div>
