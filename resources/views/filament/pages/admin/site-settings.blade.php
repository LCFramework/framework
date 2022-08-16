<form wire:submit.prevent="submit" class="space-y-6">
    {{$this->form}}

    <div>
        <x-filament-support::button type="submit">
            Save
        </x-filament-support::button>
    </div>
</form>
