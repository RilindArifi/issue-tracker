<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $issue->title }}</h2>
            <a href="{{ route('issues.edit', $issue) }}"
               class="text-sm text-gray-600 hover:text-gray-900">{{ __('Edit issue') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('partials.flash')

            {{-- Meta --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">{{ __('Project') }}</dt>
                        <dd><a href="{{ route('projects.show', $issue->project) }}"
                               class="text-indigo-600 hover:underline">{{ $issue->project->name }}</a></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Status') }}</dt>
                        <dd><span class="text-xs px-2 py-1 rounded-full {{ $issue->status->color() }}">{{ $issue->status->label() }}</span></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Priority') }}</dt>
                        <dd><span class="text-xs px-2 py-1 rounded-full {{ $issue->priority->color() }}">{{ $issue->priority->label() }}</span></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Due date') }}</dt>
                        <dd class="text-gray-900">{{ $issue->due_date?->format('M j, Y') ?? '—' }}</dd>
                    </div>
                </dl>

                @if ($issue->description)
                    <div class="mt-4">
                        <dt class="text-gray-500 text-sm">{{ __('Description') }}</dt>
                        <p class="mt-1 text-gray-800 whitespace-pre-line">{{ $issue->description }}</p>
                    </div>
                @endif
            </div>

            {{-- Tags (static for now; AJAX attach/detach added in section 5) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-3">{{ __('Tags') }}</h3>
                <div class="flex flex-wrap gap-2">
                    @forelse ($issue->tags as $tag)
                        @include('partials.tag-badge', ['tag' => $tag])
                    @empty
                        <span class="text-sm text-gray-500">{{ __('No tags.') }}</span>
                    @endforelse
                </div>
            </div>

            {{-- Members (static for now; AJAX assignment added in section 7) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-3">{{ __('Members') }}</h3>
                <div class="flex flex-wrap gap-2">
                    @forelse ($issue->members as $member)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                            {{ $member->name }}
                        </span>
                    @empty
                        <span class="text-sm text-gray-500">{{ __('No members assigned.') }}</span>
                    @endforelse
                </div>
            </div>

            {{-- Comments (static for now; AJAX paginated load + create added in section 6) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-3">{{ __('Comments') }}</h3>
                <div class="space-y-3">
                    @forelse ($issue->comments as $comment)
                        @include('partials.comment-item', ['comment' => $comment])
                    @empty
                        <span class="text-sm text-gray-500">{{ __('No comments yet.') }}</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
