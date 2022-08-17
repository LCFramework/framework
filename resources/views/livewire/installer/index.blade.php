<form wire:submit.prevent="submit">
    {{$this->form}}

    <x-filament::modal id="exception-modal">
        <x-slot name="header">
            Exception
        </x-slot>

        {{$this->exceptionMessage}}
    </x-filament::modal>
</form>
