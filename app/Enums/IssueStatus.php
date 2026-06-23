<?php

namespace App\Enums;

enum IssueStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Closed = 'closed';

    /**
     * Human-readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::InProgress => 'In Progress',
            self::Closed => 'Closed',
        };
    }

    /**
     * Tailwind classes for a status badge.
     */
    public function color(): string
    {
        return match ($this) {
            self::Open => 'bg-blue-100 text-blue-800',
            self::InProgress => 'bg-amber-100 text-amber-800',
            self::Closed => 'bg-green-100 text-green-800',
        };
    }

    /**
     * value => label map for select inputs.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
