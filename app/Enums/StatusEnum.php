<?php

namespace App\Enums;

enum StatusEnum: string
{
    case Subscribed = 'subscribed';
    case Unsubscribed = 'unsubscribed';
    case Pending = 'pending';
}
