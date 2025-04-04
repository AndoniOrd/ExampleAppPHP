<?php

namespace App\Enums;

enum MailingListTypeEnum: string
{
    case Newsletter = 'newsletter';
    case Promotional = 'promotional';
    case Transactional = 'transactional';
}