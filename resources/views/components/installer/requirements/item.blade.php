@props(['installed'])

<li class="flex items-center space-x-2">
    <div
        @class(['text-success-500' => $installed, 'text-danger-500' => !$installed])
    >
        @if($installed)
            @svg('heroicon-o-check-circle', 'h-6 w-6')
        @else
            @svg('heroicon-o-x-circle', 'h-6 w-6')
        @endif
    </div>

    <div class="font-medium">
        {{$slot}}
    </div>
</li>
