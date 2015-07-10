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

use League\CommonMark\ContextInterface;
use League\CommonMark\Inline\Parser\AbstractInlineParser;
use League\CommonMark\InlineParserContext;

class AttributesInlineParser extends AbstractInlineParser
{
    public function getCharacters()
    {
        return [' ', '{'];
    }

    public function parse(ContextInterface $context, InlineParserContext $inlineContext)
    {
        $cursor = $inlineContext->getCursor();
        if ($cursor->getFirstNonSpaceCharacter() !== '{') {
            return false;
        }

        $char = $cursor->getCharacter();
        if ('{' === $char) {
            $char = (string) $cursor->getCharacter($cursor->getPosition() - 1);
        }

        $attributes = AttributesUtils::parse($cursor);
        if (empty($attributes)) {
            return false;
        }

        $inlineContext->getInlines()->add(new InlineAttributes($attributes, ' ' === $char || '' === $char));

        return true;
    }
}
