<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Set\LaravelSetList;

return static function (RectorConfig $rectorConfig): void {
    // Define the paths to be processed
    $rectorConfig->paths([
        __DIR__ . '/app',
        __DIR__ . '/routes',
        __DIR__ . '/database',
        // Add other directories as needed
    ]);

    // Apply the desired Rector sets
    $rectorConfig->sets([
        LaravelSetList::LARAVEL_110, // Adjust to your target Laravel version
    ]);
};
