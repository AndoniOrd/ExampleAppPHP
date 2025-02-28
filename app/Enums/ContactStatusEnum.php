<?php

namespace App\Enums;

enum ContactStatusEnum: string
{
    case Subscribed = 'subscribed';
    case Unsubscribed = 'unsubscribed';
    case Pending = 'pending';
}
