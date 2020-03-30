<?php

$finder = \PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude(['bootstrap', 'storage', 'vendor'])
    ->name('*.php')
    ->name('_ide_helper')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        'blank_line_after_opening_tag' => true,
        'compact_nullable_typehint' => true,
        'declare_equal_normalize' => ['space' => 'none'],
        'function_typehint_space' => true,
        'new_with_braces' => true,
        'no_empty_statement' => true,
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,
        'no_whitespace_in_blank_line' => true,
        'return_type_declaration' => ['space_before' => 'none'],
        'single_trait_insert_per_statement' => true,
    ])->setFinder($finder);
