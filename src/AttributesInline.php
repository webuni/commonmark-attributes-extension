<?php

declare(strict_types=1);

/*
 * This is part of the webuni/commonmark-attributes-extension package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 * (c) Webuni s.r.o. <info@webuni.cz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webuni\CommonMark\AttributesExtension;

use League\CommonMark\Inline\Element\AbstractInline;

final class AttributesInline extends AbstractInline
{
    public $attributes;

    public $block;

    public function __construct(array $attributes, bool $block)
    {
        $this->attributes = $attributes;
        $this->block = $block;
        $this->data = ['delim' => true];
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function isBlock(): bool
    {
        return (bool) $this->block;
    }
}
