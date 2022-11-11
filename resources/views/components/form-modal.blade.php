@props([
    'title' => '',
    'num_features' => 0,
    'feature' => null,
    'action' => null
])
<form {{ $attributes->merge() }}>
    <x-card :title="__($title)">
        @isset($action)
            <x-slot name="action">
                {!! $action !!}
            </x-slot>
        @endif

        @if($feature && $num_features < 3)
            <div class="mb-4 shadow-md alert alert-warning">
                <div class="flex-1 font-semibold text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 w-6 h-6 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    You have {{ $num_features }} {{ Str::of($feature)->plural($num_features) }} left.
                </div>
            </div>
        @endif

        {{ $slot }}
        
        @isset($footer)
            <x-slot name="footer">
                <div class="flex items-center justify-between">
                    {{ $footer }}
                </div>
            </x-slot>
        @endisset
    </x-card>
</form>