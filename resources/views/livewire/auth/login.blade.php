<form wire:submit.prevent="authenticate">
    {{$this->form}}

    <x-filament-support::button>
        Sign in
    </x-filament-support::button>
</form>
