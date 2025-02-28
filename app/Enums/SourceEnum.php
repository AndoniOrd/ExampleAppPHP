<?php

namespace App\Enums;

enum SourceEnum: string
{
    case Web = 'web';
    case Api = 'api';
    case Manual = 'manual';
    case Import = 'import';
}
