<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\FunctionLike\FunctionLikeToFirstClassCallableRector;
use Rector\Config\RectorConfig;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/tests',
    ])
    ->withSkip([
        __DIR__.'/app/Filament', // Skip Filament auto-generated files
        __DIR__.'/vendor',
        __DIR__.'/bootstrap',
        __DIR__.'/config',
        __DIR__.'/database',
        __DIR__.'/public',
        __DIR__.'/resources',
        __DIR__.'/routes',
        __DIR__.'/storage',

        // Skip specific rules that might cause issues
        ReadOnlyPropertyRector::class,
        EncapsedStringsToSprintfRector::class,
        DisallowedEmptyRuleFixerRector::class,
    ])
    ->withPhpSets(php83: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        earlyReturn: true,
    )
    ->withSets([
        LaravelSetList::LARAVEL_110,
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL,
        LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES,
    ]);
