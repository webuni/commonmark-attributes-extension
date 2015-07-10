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
use League\CommonMark\Block\Element\Document;
use League\CommonMark\ContextInterface;

class AttributesDocument extends Document
{
    public function finalize(ContextInterface $context)
    {
        parent::finalize($context);

        $this->applyAttributes($this);

        $parent = $this->getParent();
        $parent->removeChild($this);
        foreach ($this->getChildren() as $child) {
            $parent->addChild($child);
        }
    }

    private function applyAttributes(AbstractBlock $block)
    {
        $previous = null;
        $prepend = null;
        foreach ($block->getChildren() as $key => $child) {
            if ($child instanceof Attributes) {
                if ($child->isPrepend()) {
                    $prepend = $child;
                } elseif ($child->isAppend()) {
                    $previous->data['attributes'] = AttributesUtils::merge($previous ?: $block, $child->getAttributes());
                }

                $block->removeChild($child);
            } else {
                if (isset($prepend)) {
                    $child->data['attributes'] = AttributesUtils::merge($child, $prepend->getAttributes());
                }
                $prepend = null;

                $this->applyAttributes($child);
                $previous = $child;
            }
        }

        if (isset($prepend)) {
            $block->data['attributes'] = AttributesUtils::merge($block, $prepend->getAttributes());
        }

        return $block;
    }
}
