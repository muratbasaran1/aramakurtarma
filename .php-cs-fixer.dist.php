<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__.'/app',
        __DIR__.'/bootstrap',
        __DIR__.'/config',
        __DIR__.'/database',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ])
    ->name('*.php')
    ->ignoreVCS(true)
    ->exclude(['vendor', 'storage', 'node_modules']);

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
