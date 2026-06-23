{{-- Reusable tag badge. Expects $tag (App\Models\Tag). --}}
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
      style="background-color: {{ $tag->color ? $tag->color.'22' : '#e5e7eb' }}; color: {{ $tag->color ?? '#374151' }};">
    {{ $tag->name }}
</span>
