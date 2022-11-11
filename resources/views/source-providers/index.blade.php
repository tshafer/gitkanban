
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Source Providers') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="p-4 overflow-hidden bg-white shadow-xl sm:rounded-lg">
                @foreach($source_providers as $name => $source)
                    <div class="mb-8 rounded-md shadow-md last:mb-0">
                        <div class="px-5 py-3 border-b">
                            <div class="flex items-center gap-2 justify-content">
                                <div class="flex-1">
                                    <h2 class="mb-2 text-gray-900">
                                        <span>{{ $name }}</span>
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
                                @isset($providers[$source['type']])
                                    @forelse($providers[$source['type']] as $provider)

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
                                                <div class="text-gray-500 font-semi-bold">{{ $provider->total_repositories }}</div>
                                            </td>
                                            <td class="p-4 pl-0">
                                                <div class="mb-1 text-gray-300">{{ __('Added on') }}</div>
                                                <div class="text-gray-500 tooltip tooltip-info" data-tip="{{ $provider->created_at->diffForHumans() }}">{{ $provider->created_at->format('m-d-y') }}</div>
                                            </td>

                                            <td class="max-w-sm gap-3 p-4 text-right">

                                                <x-button xs secondary wire:click="$emit('modal.open', 'source-providers.update', {'source_provider': {{ $provider->id }}})">
                                                    {{ _('Edit') }}
                                                </x-button>

                                                <x-button xs warning wire:click="confirmDestroy({{ $provider->id }})">
                                                    {{ _('Trash') }}
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
        </div>
    </div>
</div>

