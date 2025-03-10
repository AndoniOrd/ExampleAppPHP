<?php

namespace App\Enums;

enum TrackingOptions: string
{
    case OPENS = 'opens';
    case CLICKS = 'clicks';
    case BOUNCES = 'bounces';
    case ALL = 'all';
    case NONE = 'none';
}