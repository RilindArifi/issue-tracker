{{-- Reusable comment row. Rendered both in Blade and from the AJAX endpoint. Expects $comment. --}}
<div class="border border-gray-100 rounded-md p-3">
    <div class="flex items-center justify-between">
        <span class="font-medium text-gray-900 text-sm">{{ $comment->author_name }}</span>
        <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
    </div>
    <p class="mt-1 text-sm text-gray-700 whitespace-pre-line">{{ $comment->body }}</p>
</div>
