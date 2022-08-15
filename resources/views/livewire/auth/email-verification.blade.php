<div class="space-y-6">
    <form wire:submit.prevent="submit">
        {{$this->form}}
    </form>

    <div class="grid md:grid-cols-2 gap-6">
        <div>
            <x-filament-support::button
                class="w-full"
                wire:click.prevent="submit"
                wire:loading.attr="disabled"
                wire:target="submit"
            >
                Resend
            </x-filament-support::button>
        </div>

        <div>
            <form action="{{route('logout')}}" method="post">
                @csrf

                <x-filament-support::button
                    type="submit"
                    color="danger"
                    class="w-full"
                    wire:loading.attr="disabled"
                    wire:target="submit"
                >
                    Logout
                </x-filament-support::button>
            </form>
        </div>
    </div>

</div>
