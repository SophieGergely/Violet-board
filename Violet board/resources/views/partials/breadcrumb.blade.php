<nav aria-label="breadcrumb" class="page-breadcrumb {{ $extraClass ?? '' }}">
    @foreach ($items as $item)
        @if (!$loop->first)
            <span class="page-breadcrumb-sep">›</span>
        @endif
        @if (!$loop->last && isset($item['url']))
            <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
        @else
            <span class="page-breadcrumb-current">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
