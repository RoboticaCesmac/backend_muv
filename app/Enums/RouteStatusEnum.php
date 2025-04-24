<?php

namespace App\Enums;

enum RouteStatusEnum: string
{
    case InProgress = 'Em andamento';
    case Completed = 'Finalizado';
    case Cancelled = 'Cancelado';

    /**
     * Get the ID associated with this status from the database.
     */
    public static function getId(self $value): ?int
    {
        $status = \App\Models\RouteStatus::where('description', $value->value)->first();
        return $status?->id;
    }

    /**
     * Get all cases as an array for select options or validation.
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get a case by its value.
     */
    public static function fromValue(string $value): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }
        
        return null;
    }
} 