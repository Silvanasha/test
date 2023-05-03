<?php

declare(strict_types=1);

use WayOfDev\PhpCsFixer\Config\ConfigBuilder;
use WayOfDev\PhpCsFixer\Config\RuleSets\DefaultSet;

require_once 'vendor/autoload.php';

return ConfigBuilder::createFromRuleSet(new DefaultSet())
    ->inDir(__DIR__ . '/src')
    ->inDir(__DIR__ . '/tests')
    ->addFiles([__FILE__])
    ->getConfig();

//$finder = (new PhpCsFixer\Finder())
//    ->in(__DIR__)
//    ->exclude('var')
//;
//
//return (new PhpCsFixer\Config())
//    ->setRules([
//        '@Symfony' => true,
//        '@PSR2' => true,
//    ])
//    ->setFinder($finder)
//;
