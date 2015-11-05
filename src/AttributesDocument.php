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

use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\ListBlock;
use League\CommonMark\Block\Element\ListItem;
use League\CommonMark\ContextInterface;
use League\CommonMark\Node\NodeWalker;

class AttributesDocument extends Document
{
    public function finalize(ContextInterface $context, $endLineNumber = null)
    {
        parent::finalize($context, $endLineNumber);

        $this->applyAttributes($this->walker());

        $parent = $this->parent();
        $this->detach();
        foreach ($this->children() as $child) {
            $parent->appendChild($child);
        }
    }

    private function applyAttributes(NodeWalker $walker)
    {
        while (($event = $walker->next())) {
            $node = $event->getNode();
            if (!$node instanceof Attributes || $event->isEntering()) {
                continue;
            }

            $isLastChild = $node->parent()->lastChild() === $node;

            if (!$node->parent() instanceof Document && $isLastChild) {
                $target = $node->parent();
            } elseif ($node->endsWithBlankLine() || $isLastChild) {
                $target = $node->previous();
            } else {
                $target = $node->next();
            }

            if ($target && ($parent = $target->parent()) instanceof ListItem  && $parent->parent() instanceof ListBlock && $parent->parent()->isTight()) {
                $target = $parent;
            }

            if ($target) {
                $target->data['attributes'] = AttributesUtils::merge($target, $node->getAttributes());
            }

            $node->detach();
        }
    }
}
