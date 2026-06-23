<?php

namespace App\Models;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Issue extends Model
{
    /** @use HasFactory<\Database\Factories\IssueFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'status' => IssueStatus::class,
            'priority' => IssuePriority::class,
            'due_date' => 'date',
        ];
    }

    /**
     * The project this issue belongs to.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Comments on this issue, newest first.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    /**
     * Tags attached to this issue.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Users assigned to this issue.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Filter by status (accepts enum or raw value). No-op for empty filter.
     */
    public function scopeStatus(Builder $query, IssueStatus|string|null $status): Builder
    {
        return $query->when($status, fn (Builder $q) => $q->where(
            'status',
            $status instanceof IssueStatus ? $status->value : $status
        ));
    }

    /**
     * Filter by priority (accepts enum or raw value). No-op for empty filter.
     */
    public function scopePriority(Builder $query, IssuePriority|string|null $priority): Builder
    {
        return $query->when($priority, fn (Builder $q) => $q->where(
            'priority',
            $priority instanceof IssuePriority ? $priority->value : $priority
        ));
    }

    /**
     * Filter to issues carrying a given tag id. No-op for empty filter.
     */
    public function scopeTag(Builder $query, int|string|null $tagId): Builder
    {
        return $query->when($tagId, fn (Builder $q) => $q->whereHas(
            'tags',
            fn (Builder $tq) => $tq->where('tags.id', $tagId)
        ));
    }

    /**
     * Full-text-ish search across title and description. No-op for empty term.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when(filled($term), function (Builder $q) use ($term) {
            $q->where(function (Builder $sub) use ($term) {
                $sub->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });
        });
    }
}
