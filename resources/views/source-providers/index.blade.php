<x-settings>
    <x-action-section>
        <x-slot name="title">
            <x-eos-source-o class="inline-block w-5 h-5" /> {{ __('Source Providers') }}
        </x-slot>
        <div class="py-4">
            @foreach($source_providers as $name => $source)
                <div class="mx-4 mb-8 rounded-md shadow-md last:mb-0">
                    <div class="px-5 py-3 border-b">
                        <div class="flex items-center gap-2 justify-content">
                            <div class="flex-1">
                                <h2 class="mb-2 text-gray-900">
                                    @svg($source['icon'], ['class' => 'inline-block w-5 h-5 mr-3'])<span>{{ $name }}</span>
                                </h2>
                            </div>
                            <div class="text-left flex-inital">
                                <x-button sm primary :href="$source['url']">
                                    {{ __('Link new :name account', ['name' => $name]) }}
                                </x-button>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400"> Before you link a new {{ $name }} account, make sure you're logged in to the correct GIT account. </p>
                    </div>
                    <table class="w-full table-compact">
                        <tbody>
                            @isset($all_providers[$source['type']])
                                @forelse($all_providers[$source['type']] as $provider)
                                    <tr @class([
                                        'border-t' => !$loop->first,
                                        'border-gray-200',
                                        'bg-gray-50' => $loop->even,
                                    ])>
                                        <td class="p-4">
                                            <div class="mb-1 text-gray-300">{{ __('Label') }}</div>
                                            <div class="text-gray-500 font-semi-bold">{{ $provider->label }}</div>
                                        </td>
                                        <td class="p-4">
                                            <div class="mb-1 text-gray-300">{{ __('Name') }}</div>
                                            <div class="text-gray-500 font-semi-bold">{{ $provider->name }}</div>
                                        </td>
                                        <td class="p-4">
                                            <div class="mb-1 text-gray-300">{{ __('Repositories') }}</div>
                                            <div class="text-gray-500 font-semi-bold">0</div>
                                        </td>
                                        <td class="p-4 pl-0">
                                            <div class="mb-1 text-gray-300">{{ __('Added on') }}</div>
                                            <div class="text-gray-500 tooltip tooltip-info" data-tip="{{ $provider->created_at->diffForHumans() }}">{{ $provider->created_at->format(format_date()) }}</div>
                                        </td>
                                    
                                    
                                        <td class="max-w-sm p-4 text-right">
                                            <x-button xs wire:click.defer="startEdit('{{ $provider->id }}')" class="group tooltip tooltip-info" data-tip="{{ __('Edit Source Provider') }}">
                                                <x-heroicon-o-pencil class="w-6 h-6 text-orange-500 group-hover:text-orange-400" /> 
                                            </x-button>
                                        
                                            <x-button xs wire:click.defer="confirmDestroy('{{ $provider->id }}')" class="group tooltip tooltip-info" data-tip="{{ __('Delete Source Provider') }}">
                                                <x-heroicon-o-trash class="w-6 h-6 text-red-500 group-hover:text-red-400" /> 
                                            </x-button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="p-4 text-center" colspan="5">
                                        <div class="text-gray-500">{{ __('No :provider accounts linked.', ['provider' => $name]) }}</div>
                                    </td>
                                </tr>
                            @endforelse
                    </tbody>
                    </table>                
                </div>        
            @endforeach
        </div>
    </x-action-section>
</x-settings>