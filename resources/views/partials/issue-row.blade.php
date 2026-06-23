{{-- Reusable issue row. Rendered in Blade and from the AJAX search endpoint. Expects $issue. --}}
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
