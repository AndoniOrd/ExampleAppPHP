<?php

namespace App\Enums;

enum EmailTemplateStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Borrador',
            self::ACTIVE => 'Activo',
            self::ARCHIVED => 'Archivado',
        };
    }
}