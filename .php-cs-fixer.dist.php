<?php

declare(strict_types = 1);

$directories = [
    __DIR__ . '/config',
    __DIR__ . '/backend/app',
    __DIR__ . '/backend/config',
    __DIR__ . '/backend/database',
    __DIR__ . '/backend/routes',
    __DIR__ . '/backend/tests',
];

$existingDirectories = array_values(array_filter($directories, static function (string $path): bool {
    return is_dir($path);
}));

$finder = PhpCsFixer\Finder::create();

if ($existingDirectories !== []) {
    $finder->in($existingDirectories);
} else {
    $finder->in([__DIR__])
        ->depth('== 0');
}

$finder
    ->name('*.php')
    ->ignoreVCS(true)
    ->exclude([
        'vendor',
        'storage',
        'node_modules',
        'backend/vendor',
        'backend/storage',
        'backend/node_modules',
        'backend/bootstrap/cache',
    ]);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'single_quote' => true,
        'declare_strict_types' => true,
        'blank_line_before_statement' => [
            'statements' => ['return', 'try', 'if', 'for', 'foreach', 'while'],
        ],
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_to_comment' => false,
        'native_function_invocation' => [
            'include' => ['@compiler_optimized'],
            'scope' => 'namespaced',
        ],
    ]);
