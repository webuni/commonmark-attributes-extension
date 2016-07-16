<?php

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
use League\CommonMark\Block\Element\ListBlock;
use League\CommonMark\Block\Element\ListItem;
use League\CommonMark\DocumentProcessorInterface;

class AttributesProcessor implements DocumentProcessorInterface
{
    const DIRECTION_PREFIX = 'prefix';
    const DIRECTION_SUFFIX = 'suffix';

    public function processDocument(Document $document)
    {
        $walker = $document->walker();
        while ($event = $walker->next()) {
            $node = $event->getNode();

            if ($event->isEntering() || !$node instanceof Attributes) {
                continue;
            }

            list($target, $direction) = $this->findTargetAndDirection($node);

            if ($target) {
                if (($parent = $target->parent()) instanceof ListItem && $parent->parent() instanceof ListBlock && $parent->parent()->isTight()) {
                    $target = $parent;
                }

                if ($direction === self::DIRECTION_SUFFIX) {
                    $attributes = AttributesUtils::merge($target, $node->getAttributes());
                } else {
                    $attributes = AttributesUtils::merge($node->getAttributes(), $target);
                }

                $target->data['attributes'] = $attributes;
            }

            if ($node->endsWithBlankLine() && $node->next() && $node->previous()) {
                $node->previous()->setLastLineBlank(true);
            }

            $node->detach();
        }
    }

    private function findTargetAndDirection(AbstractBlock $node)
    {
        $target = null;
        $direction = null;
        $previous = $next = $node;
        while (true) {
            $previous = $this->getPrevious($previous);
            $next = $this->getNext($next);

            if ($previous === null && $next === null) {
                $target = $node->parent();
                $direction = self::DIRECTION_SUFFIX;
                break;
            }

            if ($previous !== null && !$previous instanceof Attributes) {
                $target = $previous;
                $direction = self::DIRECTION_SUFFIX;
                break;
            }

            if ($next !== null && !$next instanceof Attributes) {
                $target = $next;
                $direction = self::DIRECTION_PREFIX;
                break;
            }
        }

        return [$target, $direction];
    }

    private function getPrevious(AbstractBlock $node = null)
    {
        $previous = $node instanceof AbstractBlock ? $node->previous() : null;

        if ($previous && $previous->endsWithBlankLine()) {
            $previous = null;
        }

        return $previous;
    }

    private function getNext(AbstractBlock $node = null)
    {
        if ($node instanceof AbstractBlock) {
            return $node->endsWithBlankLine() ? null : $node->next();
        }
    }
}
