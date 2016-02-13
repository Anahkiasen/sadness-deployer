<?php
use PhpCsFixer\Config;
use PhpCsFixer\Finder;

require 'vendor/autoload.php';

$finder = Finder::create()->in(['src']);

return Config::create()
             ->setRiskyAllowed(true)
             ->setRules([
                 '@Symfony'                                  => true,
                 'echo_to_print'                             => true,
                 'ereg_to_preg'                              => true,
                 'linebreak_after_opening_tag'               => true,
                 'no_blank_lines_before_namespace'           => true,
                 'no_multiline_whitespace_before_semicolons' => true,
                 'no_php4_constructor'                       => true,
                 'no_short_echo_tag'                         => true,
                 'ordered_imports'                           => true,
                 'php_unit_construct'                        => true,
                 'php_unit_strict'                           => false,
                 'phpdoc_order'                              => true,
                 'phpdoc_property'                           => true,
                 'psr0'                                      => true,
                 'short_array_syntax'                        => true,
                 'strict'                                    => true,
                 'strict_param'                              => true,
             ])
             ->setUsingCache(true)
             ->finder($finder);
