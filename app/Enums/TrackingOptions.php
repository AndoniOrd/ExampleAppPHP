<?php

namespace App\Enums;

enum TrackingOptions: string
{
    case OPEN_CLICK = 'open_click';
    case CLICKS = 'clicks'; 
    case BOUNCES = 'bounces';
    case ALL = 'all';
    case NONE = 'none';
}