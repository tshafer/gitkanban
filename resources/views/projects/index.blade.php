<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ __('Projects') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="p-4 overflow-hidden bg-white shadow-xl sm:rounded-lg">

            <div class="px-4 py-5 bg-white border-b border-gray-200 sm:px-6">
                <div class="flex flex-wrap items-center justify-between -mt-2 -ml-4 sm:flex-nowrap">
                    <div class="mt-2 ml-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Projects</h3>
                    </div>
                    <div class="flex-shrink-0 mt-2 ml-4">
                        <x-button dark wire:click="$emit('openModal', 'projects.create')" >{{ __('Create Project') }}</x-button>
                    </div>
                </div>
            </div>

            @if($projects->isNotEmpty())
                <table class="w-full table-compact">
                    <tbody>
                        @foreach($projects as $project)
                            <tr @class([
                                'border-t' => !$loop->first,
                                'border-gray-200',
                                'bg-gray-50' => $loop->even,
                            ])>
                                <td class="p-4">
                                    <div class="mb-1 text-gray-300">{{ __('Name') }}</div>
                                    <div class="text-gray-500 font-semi-bold">{{ $project->name }}</div>
                                </td>

                                <td class="p-4">
                                    <div class="mb-1 text-gray-300">{{ __('foo') }}</div>
                                </td>

                                <td class="p-4">
                                    <div class="mb-1 text-gray-300">{{ __('bar') }}</div>
                                </td>

                                <td class="p-4 pl-0">
                                    <div class="mb-1 text-gray-300">{{ __('Added') }}</div>
                                    <div class="text-gray-500 tooltip tooltip-info" data-tip="{{ $project->created_at->format('m-d-y') }}">{{ $project->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="max-w-sm gap-3 p-4 text-right">
                                    <x-button xs secondary wire:click="$emit('modal.open', 'projects.update', {'project': {{ $project->id }}})">
                                        {{ _('Edit') }}
                                    </x-button>

                                    <x-button xs warning wire:click="confirmDestroy({{ $project->id }})">
                                        {{ _('Trash') }}
                                    </x-button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="w-1/4 p-5 m-auto mt-5 text-center ">
                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No Projects') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('Get started by creating a Project.') }}
                    </p>
                    <div class="mt-6">
                        <x-button dark wire:click="$emit('modal.open', 'projects.create')">
                            {{ __('Create Project') }}
                        </x-button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

