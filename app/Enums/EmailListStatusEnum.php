<?php

namespace App\Enums;

enum EmailListStatusEnum: string
{
    case Active = 'active';
    case Draft = 'draft';
    case Archived = 'achived';
}