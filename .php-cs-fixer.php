<?php

$finder = Symfony\Component\Finder\Finder::create()
    ->in([
        __DIR__ . '/php',
        __DIR__ . '/app/app',
        __DIR__ . '/app/config',
        __DIR__ . '/app/database',
        __DIR__ . '/app/lang',
        __DIR__ . '/app/routes',
        __DIR__ . '/app/tests',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PHP70Migration'                        => true,
        '@PHP71Migration'                        => true,
        '@PHP73Migration'                        => true,
        '@PHP74Migration'                        => true,
        '@PHP80Migration'                        => true,
        '@PSR2'                                  => true,
        'array_indentation'                      => true,
        'binary_operator_spaces'                 => ['default' => 'align_single_space_minimal'],
        'blank_line_before_statement'            => ['statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try']],
        'class_attributes_separation'            => ['elements' => ['method' => 'one']],
        'concat_space'                           => ['spacing' => 'one'],
        'increment_style'                        => ['style' => 'post'],
        'method_argument_space'                  => ['on_multiline' => 'ensure_fully_multiline',   'keep_multiple_spaces_after_comma' => true],
        'method_chaining_indentation'            => true,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'no_extra_blank_lines'                   => true,
        'no_trailing_comma_in_singleline_array'  => true,
        'no_unused_imports'                      => true,
        'no_whitespace_before_comma_in_array'    => true,
        'not_operator_with_successor_space'      => false,
        'ordered_imports'                        => true,
        'phpdoc_scalar'                          => true,
        'phpdoc_single_line_var_spacing'         => true,
        'phpdoc_var_without_name'                => true,
        'return_type_declaration'                => ['space_before' => 'none'],
        'semicolon_after_instruction'            => false,
        'simple_to_complex_string_variable'      => true,
        'strict_comparison'                      => true,
        'ternary_operator_spaces'                => true,
        'trailing_comma_in_multiline'            => true,
        'trim_array_spaces'                      => true,
        'unary_operator_spaces'                  => true,
        'yoda_style'                             => false,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setUsingCache(false);
