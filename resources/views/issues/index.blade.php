<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Issues') }}</h2>
            <a href="{{ route('issues.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-xs font-semibold uppercase tracking-widest rounded-md hover:bg-gray-700">
                {{ __('New issue') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('partials.flash')

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @include('partials.issue-filters')
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @forelse ($issues as $issue)
                    <div class="flex items-center justify-between border-b border-gray-100 py-3 last:border-0">
                        <div>
                            <a href="{{ route('issues.show', $issue) }}"
                               class="font-medium text-indigo-600 hover:underline">{{ $issue->title }}</a>
                            <p class="text-sm text-gray-500">{{ $issue->project->name }}</p>
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
                    <p class="text-gray-500">{{ __('No issues match these filters.') }}</p>
                @endforelse

                <div class="mt-4">{{ $issues->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
