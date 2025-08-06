<?php

declare(strict_types=1);
use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = (new Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new Config())
    ->setRules([
        'no_unused_imports' => true,
        'declare_strict_types' => true,
        '@Symfony' => true,
        '@PhpCsFixer' => true,
        '@PSR2' => true,
        '@PHP82Migration' => true,
    ])
    ->setRiskyAllowed(true)
    ->setIndent(str_pad(' ', 4))
    ->setLineEnding("\n")
    ->setFinder($finder)
;
