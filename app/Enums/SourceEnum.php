<?php

namespace App\Enums;

enum SourceEnum: string
{
    case Website = 'website';
    case App = 'app';
    case Referral = 'referral';

    public static function values(): array
    {
        return array_map(fn($enum) => $enum->value, self::cases());
    }
}