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

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\Block\Element\ListBlock;
use League\CommonMark\Block\Element\ListItem;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Node\Node;

final class AttributesProcessor
{
    private const DIRECTION_PREFIX = 'prefix';

    private const DIRECTION_SUFFIX = 'suffix';

    public function processDocument(Document $document): void
    {
        $walker = $document->walker();
        while ($event = $walker->next()) {
            $node = $event->getNode();
            if (!$node instanceof AttributesInline && ($event->isEntering() || !$node instanceof Attributes)) {
                continue;
            }

            list($target, $direction) = $this->findTargetAndDirection($node);

            if ($target instanceof Node) {
                $parent = $target->parent();
                if ($parent instanceof ListItem && $parent->parent() instanceof ListBlock && $parent->parent()->isTight()) {
                    $target = $parent;
                }

                if (self::DIRECTION_SUFFIX === $direction) {
                    $attributes = AttributesUtils::merge($target, $node->getAttributes());
                } else {
                    $attributes = AttributesUtils::merge($node->getAttributes(), $target);
                }

                if ($target instanceof AbstractBlock || $target instanceof AbstractInline) {
                    $target->data['attributes'] = $attributes;
                }
            }

            if ($node instanceof AbstractBlock && $node->endsWithBlankLine() && $node->next() && $node->previous()) {
                $previous = $node->previous();
                if ($previous instanceof AbstractBlock) {
                    $previous->setLastLineBlank(true);
                }
            }

            $node->detach();
        }
    }

    private function findTargetAndDirection(Node $node): array
    {
        $target = null;
        $direction = null;
        $previous = $next = $node;
        while (true) {
            $previous = $this->getPrevious($previous);
            $next = $this->getNext($next);

            if (null === $previous && null === $next) {
                if (!$node->parent() instanceof FencedCode) {
                    $target = $node->parent();
                    $direction = self::DIRECTION_SUFFIX;
                }

                break;
            }

            if ($node instanceof AttributesInline && (null === $previous || ($previous instanceof AbstractInline && $node->isBlock()))) {
                continue;
            }

            if (null !== $previous && !$this->isAttributesNode($previous)) {
                $target = $previous;
                $direction = self::DIRECTION_SUFFIX;

                break;
            }

            if (null !== $next && !$this->isAttributesNode($next)) {
                $target = $next;
                $direction = self::DIRECTION_PREFIX;

                break;
            }
        }

        return [$target, $direction];
    }

    private function getPrevious(Node $node = null): ?Node
    {
        $previous = $node instanceof Node ? $node->previous() : null;

        if ($previous instanceof AbstractBlock && $previous->endsWithBlankLine()) {
            $previous = null;
        }

        return $previous;
    }

    private function getNext(Node $node = null): ?Node
    {
        $next = $node instanceof Node ? $node->next() : null;

        if ($node instanceof AbstractBlock && $node->endsWithBlankLine()) {
            $next = null;
        }

        return $next;
    }

    private function isAttributesNode(Node $node): bool
    {
        return $node instanceof Attributes || $node instanceof AttributesInline;
    }
}
