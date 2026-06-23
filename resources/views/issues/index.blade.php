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

    <div class="py-12" x-data="issueSearch({
            filters: {
                search: @js($filters['search'] ?? ''),
                status: @js($filters['status'] ?? ''),
                priority: @js($filters['priority'] ?? ''),
                tag: @js((string) ($filters['tag'] ?? '')),
            }
        })">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('partials.flash')

            {{-- Filters (debounced search + change-driven AJAX, no full reload) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                    <div class="lg:col-span-2">
                        <x-input-label for="search" :value="__('Search')" />
                        <x-text-input id="search" type="text" class="mt-1 block w-full"
                                      x-model="filters.search" @input.debounce.300ms="fetchIssues()"
                                      placeholder="{{ __('Title or description…') }}" />
                    </div>
                    <div>
                        <x-input-label for="status" :value="__('Status')" />
                        <select id="status" x-model="filters.status" @change="fetchIssues()"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">{{ __('All') }}</option>
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="priority" :value="__('Priority')" />
                        <select id="priority" x-model="filters.priority" @change="fetchIssues()"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">{{ __('All') }}</option>
                            @foreach ($priorities as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="tag" :value="__('Tag')" />
                        <select id="tag" x-model="filters.tag" @change="fetchIssues()"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">{{ __('All') }}</option>
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-3 sm:col-span-2 lg:col-span-5">
                        <button type="button" @click="reset()"
                                class="text-sm text-gray-600 hover:text-gray-900">{{ __('Reset') }}</button>
                        <span class="text-xs text-gray-400" x-show="loading">{{ __('Searching…') }}</span>
                        <span class="text-xs text-gray-400" x-show="!loading" x-text="`${total} result(s)`"></span>
                    </div>
                </div>
            </div>

            {{-- Results: server-rendered initially, swapped via AJAX on filter change --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                {{-- Live region: populated by Alpine when a search runs --}}
                <div x-show="touched">
                    <template x-for="(html, i) in items" :key="i">
                        <div x-html="html"></div>
                    </template>
                    <p x-show="!loading && items.length === 0" class="text-gray-500">
                        {{ __('No issues match these filters.') }}
                    </p>
                </div>

                {{-- Initial server-rendered list (hidden once the user interacts) --}}
                <div x-show="!touched">
                    @forelse ($issues as $issue)
                        @include('partials.issue-row', ['issue' => $issue])
                    @empty
                        <p class="text-gray-500">{{ __('No issues match these filters.') }}</p>
                    @endforelse

                    <div class="mt-4">{{ $issues->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
