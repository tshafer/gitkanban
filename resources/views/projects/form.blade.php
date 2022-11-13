<x-form-modal wire:submit.prevent="{{ Arr::get($properties, 'action') }}" :title="__(Arr::get($properties, 'title'))" :id="__(Arr::get($properties, 'id'))">

    <div class="mb-4">
        <x-input :label="__('Name')" wire:model.defer="form.name"/>
    </div>

    <x-slot name="footer">
        <x-button wire:click="$emit('modal.close')" >
            {{ __('Nevermind') }}
        </x-button>

        <x-button dark type="submit" spinner="{{ $properties['action'] }}">
            {{ __(ucfirst($properties['action'])) }}
        </x-button>
    </x-slot>

</x-form-modal>
