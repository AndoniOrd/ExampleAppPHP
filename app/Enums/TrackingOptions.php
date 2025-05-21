<?php

namespace App\Enums;

enum TrackingOptions: string
{
    case Open = 'open';
    case Click = 'click';
    case OpenClick = 'open_click';
    case None = 'none';
}