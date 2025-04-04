<?php

namespace App\Enums;

enum ContactStatusEnum: string
{
    case Subscribed = 'subscribed';
    case Unsubscribed = 'unsubscribed';
    case Pending = 'pending';

    public static function values(): array
    {
        return array_map(fn($enum) => $enum->value, self::cases());
    }
}
