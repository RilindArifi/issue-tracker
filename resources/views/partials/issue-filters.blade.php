{{-- Filter bar for the issues index. Expects $statuses, $priorities, $tags, $filters. --}}
<form method="GET" action="{{ route('issues.index') }}"
      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
    <div class="lg:col-span-2">
        <x-input-label for="search" :value="__('Search')" />
        <x-text-input id="search" name="search" type="text" class="mt-1 block w-full"
                      :value="$filters['search'] ?? ''" placeholder="{{ __('Title or description…') }}" />
    </div>

    <div>
        <x-input-label for="status" :value="__('Status')" />
        <select id="status" name="status"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            <option value="">{{ __('All') }}</option>
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <x-input-label for="priority" :value="__('Priority')" />
        <select id="priority" name="priority"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            <option value="">{{ __('All') }}</option>
            @foreach ($priorities as $value => $label)
                <option value="{{ $value }}" @selected(($filters['priority'] ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <x-input-label for="tag" :value="__('Tag')" />
        <select id="tag" name="tag"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            <option value="">{{ __('All') }}</option>
            @foreach ($tags as $tag)
                <option value="{{ $tag->id }}" @selected((string) ($filters['tag'] ?? '') === (string) $tag->id)>{{ $tag->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="flex items-center gap-3 sm:col-span-2 lg:col-span-5">
        <x-primary-button>{{ __('Filter') }}</x-primary-button>
        <a href="{{ route('issues.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Reset') }}</a>
    </div>
</form>
