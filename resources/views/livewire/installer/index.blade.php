<form wire:submit.prevent="submit">
    {{$this->form}}

    <div id="exception-modal">
        <x-filament::modal>
            <x-slot name="header">
                Exception
            </x-slot>

            {{$this->exceptionMessage}}
        </x-filament::modal>
    </div>
</form>
