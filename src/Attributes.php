<?php

/*
 * This is part of the webuni/commonmark-attributes-extension package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webuni\CommonMark\AttributesExtension;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

class Attributes extends AbstractBlock
{
    const PREPEND = 0;
    const APPEND = 1;

    private $attributes;
    private $direction;

    public function __construct($attributes, $direction)
    {
        parent::__construct();
        $this->attributes = $attributes;
        $this->direction = $direction;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function isPrepend()
    {
        return self::PREPEND === $this->direction;
    }

    public function isAppend()
    {
        return self::APPEND === $this->direction;
    }

    public function canContain(AbstractBlock $block)
    {
        return true;
    }

    public function acceptsLines()
    {
        return false;
    }

    public function isCode()
    {
        return false;
    }

    public function matchesNextLine(Cursor $cursor)
    {
        return false;
    }

    public function handleRemainingContents(ContextInterface $context, Cursor $cursor)
    {
    }
}
