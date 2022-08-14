<form wire:submit.prevent="authenticate">
    {{$this->form}}

    <x-filament-support::button class="w-full mt-6">
        Sign in
    </x-filament-support::button>
</form>
