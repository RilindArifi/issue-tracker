<?php

namespace App\Enums;

enum IssuePriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    /**
     * Human-readable label for the priority.
     */
    public function label(): string
    {
        return match ($this) {
            self::Low => 'Low',
            self::Medium => 'Medium',
            self::High => 'High',
        };
    }

    /**
     * Tailwind classes for a priority badge.
     */
    public function color(): string
    {
        return match ($this) {
            self::Low => 'bg-gray-100 text-gray-800',
            self::Medium => 'bg-yellow-100 text-yellow-800',
            self::High => 'bg-red-100 text-red-800',
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
