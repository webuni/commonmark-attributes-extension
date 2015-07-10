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
use League\CommonMark\Block\Element\ListItem;
use League\CommonMark\HtmlElement;
use League\CommonMark\HtmlRenderer as BaseHtmlRenderer;
use League\CommonMark\Inline\Element\AbstractInline;

class HtmlRenderer extends BaseHtmlRenderer
{
    public function renderBlock(AbstractBlock $block, $inTightList = false)
    {
        $element = parent::renderBlock($block, $inTightList);

        if (isset($block->data['attributes']) && $element instanceof HtmlElement) {
            $this->mergeAttributes($element, $block->data['attributes']);
        } elseif (isset($block->data['attributes']) && $block instanceof ListItem) {
            $element = new HtmlElement('li', $block->data['attributes'], substr($element, 4, -5));
        }

        return $element;
    }

    protected function renderInline(AbstractInline $inline)
    {
        $element = parent::renderInline($inline);

        if (isset($inline->data['attributes']) && $element instanceof HtmlElement) {
            $this->mergeAttributes($element, $inline->data['attributes']);
        }

        return $element;
    }

    protected function mergeAttributes(HtmlElement $element, $attributes)
    {
        $all = $element->getAllAttributes();
        foreach ((array) $attributes as $name => $value) {
            if (isset($all[$name])) {
                continue;
            }

            $element->setAttribute($name, $value);
        }
    }
}
