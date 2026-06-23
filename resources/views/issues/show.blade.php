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

            {{-- Tags (AJAX attach/detach via Alpine + fetch) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                 x-data="issueTags({
                     issueId: {{ $issue->id }},
                     tags: {{ Js::from($issue->tags->map->only(['id', 'name', 'color'])) }},
                     allTags: {{ Js::from($allTags->map->only(['id', 'name', 'color'])) }},
                 })">
                <h3 class="font-semibold text-gray-800 mb-3">{{ __('Tags') }}</h3>

                <div class="flex flex-wrap gap-2 min-h-[1.5rem]">
                    <template x-for="tag in tags" :key="tag.id">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
                              :style="`background-color: ${tag.color ? tag.color + '22' : '#e5e7eb'}; color: ${tag.color || '#374151'};`">
                            <span x-text="tag.name"></span>
                            <button type="button" @click="detach(tag)" :disabled="busy"
                                    class="ml-1 text-current/70 hover:text-current" aria-label="Remove tag">&times;</button>
                        </span>
                    </template>
                    <span x-show="tags.length === 0" class="text-sm text-gray-500">{{ __('No tags.') }}</span>
                </div>

                {{-- Attach control --}}
                <div class="mt-4 flex items-center gap-2">
                    <select x-model="selected" :disabled="busy || availableTags.length === 0"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                        <option value="">{{ __('Add a tag…') }}</option>
                        <template x-for="tag in availableTags" :key="tag.id">
                            <option :value="tag.id" x-text="tag.name"></option>
                        </template>
                    </select>
                    <button type="button" @click="attach()" :disabled="busy || !selected"
                            class="inline-flex items-center px-3 py-1.5 bg-gray-800 text-white text-xs font-semibold rounded-md hover:bg-gray-700 disabled:opacity-50">
                        {{ __('Add') }}
                    </button>
                </div>

                <p x-show="error" x-text="error" class="mt-2 text-sm text-red-600"></p>
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
