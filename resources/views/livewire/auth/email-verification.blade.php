<form wire:submit.prevent="submit">
    {{$this->form}}

    <x-filament-support::button type="submit" class="w-full mt-6">
        Resend email
    </x-filament-support::button>
</form>
