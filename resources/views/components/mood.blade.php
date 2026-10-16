@props(['category'])
<span aria-hidden="true">{!! $category->name !!}</span><span class="visually-hidden">{{ $category->label() }}</span>
