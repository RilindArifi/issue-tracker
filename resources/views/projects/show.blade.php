<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $project->name }}
            </h2>
            @can('update', $project)
                <a href="{{ route('projects.edit', $project) }}"
                   class="text-sm text-gray-600 hover:text-gray-900">{{ __('Edit project') }}</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('partials.flash')

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if ($project->description)
                    <p class="text-gray-700 whitespace-pre-line">{{ $project->description }}</p>
                @endif
                <dl class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">{{ __('Owner') }}</dt>
                        <dd class="text-gray-900">{{ $project->owner->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Start date') }}</dt>
                        <dd class="text-gray-900">{{ $project->start_date?->format('M j, Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Deadline') }}</dt>
                        <dd class="text-gray-900">{{ $project->deadline?->format('M j, Y') ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-800 mb-4">{{ __('Issues') }}</h3>

                @forelse ($issues as $issue)
                    <div class="flex items-center justify-between border-b border-gray-100 py-3 last:border-0">
                        <div>
                            <span class="font-medium text-gray-900">{{ $issue->title }}</span>
                            <div class="mt-1 flex flex-wrap gap-1">
                                @foreach ($issue->tags as $tag)
                                    @include('partials.tag-badge', ['tag' => $tag])
                                @endforeach
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs px-2 py-1 rounded-full {{ $issue->status->color() }}">{{ $issue->status->label() }}</span>
                            <span class="text-xs px-2 py-1 rounded-full {{ $issue->priority->color() }}">{{ $issue->priority->label() }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">{{ __('No issues in this project yet.') }}</p>
                @endforelse

                <div class="mt-4">
                    {{ $issues->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
