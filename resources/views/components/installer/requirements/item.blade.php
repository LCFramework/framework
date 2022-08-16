@props(['installed'])

<li class="flex items-center space-x-1">
    <div>
        @if($installed)
            @svg('heroicon-o-check-circle', 'h-5 w-5')
        @else
            @svg('heroicon-o-x-circle', 'h-5 w-5')
        @endif
    </div>

    <div>
        {{$slot}}
    </div>
</li>
