<form wire:submit.prevent="submit">
    {{$this->form}}

    <x-filament-support::button type="submit" class="w-full mt-6">
        Sign up
    </x-filament-support::button>
</form>
