<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Projects') }}
            </h2>
            <a href="{{ route('projects.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-xs font-semibold uppercase tracking-widest rounded-md hover:bg-gray-700">
                {{ __('New project') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @include('partials.flash')

                @forelse ($projects as $project)
                    <div class="flex items-center justify-between border-b border-gray-100 py-4 last:border-0">
                        <div>
                            <a href="{{ route('projects.show', $project) }}"
                               class="text-lg font-medium text-indigo-600 hover:underline">
                                {{ $project->name }}
                            </a>
                            <p class="text-sm text-gray-500">
                                {{ $project->issues_count }} {{ \Illuminate\Support\Str::plural('issue', $project->issues_count) }}
                                · {{ __('owner') }}: {{ $project->owner->name }}
                                @if ($project->deadline)
                                    · {{ __('due') }} {{ $project->deadline->format('M j, Y') }}
                                @endif
                            </p>
                        </div>
                        @can('update', $project)
                            <a href="{{ route('projects.edit', $project) }}"
                               class="text-sm text-gray-500 hover:text-gray-700">{{ __('Edit') }}</a>
                        @endcan
                    </div>
                @empty
                    <p class="text-gray-500">{{ __('No projects yet.') }}</p>
                @endforelse

                <div class="mt-4">
                    {{ $projects->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
