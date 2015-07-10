<?php

$header = <<<EOF
This is part of the webuni/commonmark-attributes-extension package.

(c) Martin Hasoň <martin.hason@gmail.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader($header);

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude(['vendor'])
    ->in([__DIR__])
;

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers([
        'header_comment',
        'align_double_arrow',
        'newline_after_open_tag',
        'ordered_use',
        'short_array_syntax',
    ])
    ->finder($finder)
;
