<form wire:submit.prevent="submit">
    {{$this->form}}

    <x-filament-support::button class="w-full mt-6">
        Sign in
    </x-filament-support::button>
</form>
