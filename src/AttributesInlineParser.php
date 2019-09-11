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

use League\CommonMark\Inline\Parser\InlineParserInterface;
use League\CommonMark\InlineParserContext;

final class AttributesInlineParser implements InlineParserInterface
{
    public function getCharacters(): array
    {
        return [' ', '{'];
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $cursor = $inlineContext->getCursor();
        if ('{' !== $cursor->getNextNonSpaceCharacter()) {
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

        if ('' === $char) {
            $cursor->advanceToNextNonSpaceOrNewline();
        }

        $node = new AttributesInline($attributes, ' ' === $char || '' === $char);
        $inlineContext->getContainer()->appendChild($node);

        return true;
    }
}
