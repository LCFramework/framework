<form wire:submit.prevent="submit">
    {{$this->form}}

    <x-filament-support::button
        type="submit"
        class="w-full mt-6"
        wire:loading.attr="disabled"
        wire:target="submit"
    >
        Reset password
    </x-filament-support::button>
</form>
