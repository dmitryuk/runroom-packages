<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$header = <<<'HEADER'
This file is part of the Runroom package.

(c) Runroom <runroom@runroom.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
HEADER;

$finder = Finder::create()
    ->exclude(['var'])
    ->in(__DIR__);

$config = new Config();

$config->setRules([
    '@Symfony' => true,
    '@Symfony:risky' => true,
    'array_syntax' => ['syntax' => 'short'],
    'combine_consecutive_issets' => true,
    'combine_consecutive_unsets' => true,
    'concat_space' => ['spacing' => 'one'],
    'declare_strict_types' => true,
    'header_comment' => ['header' => $header],
    'no_extra_blank_lines' => true,
    'no_php4_constructor' => true,
    'no_useless_else' => true,
    'no_useless_return' => true,
    'ordered_class_elements' => true,
    'ordered_imports' => true,
    'phpdoc_align' => ['align' => 'left'],
    'phpdoc_order' => true,
    'phpdoc_to_comment' => ['ignored_tags' => ['psalm-suppress', 'phpstan-ignore-next-line', 'todo', 'return', 'var']],
    'compact_nullable_typehint' => true,
    'void_return' => false,
    'strict_comparison' => true,
    'strict_param' => true,
    'php_unit_strict' => true,
    'php_unit_test_annotation' => ['style' => 'annotation'],
    'php_unit_test_case_static_method_calls' => true,
])
->setRiskyAllowed(true)
->setFinder($finder);

return $config;
