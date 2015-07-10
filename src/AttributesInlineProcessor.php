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

use League\CommonMark\Block\Element\ListItem;
use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Delimiter\DelimiterStack;
use League\CommonMark\Inline\Processor\InlineProcessorInterface;
use League\CommonMark\Util\ArrayCollection;

class AttributesInlineProcessor implements InlineProcessorInterface
{
    public function processInlines(ArrayCollection $inlines, DelimiterStack $delimiterStack, Delimiter $stackBottom = null)
    {
        $previous = null;
        foreach ($inlines as $key => $inline) {
            if (!$inline instanceof InlineAttributes) {
                $previous = $inline;
                continue;
            }

            $inlines->remove($key);

            if (0 === count($inline->getAttributes())) {
                continue;
            }

            $node = null;
            if ($inline->isBlock()) {
                foreach (debug_backtrace(false) as $trace) {
                    if ('League\CommonMark\DocParser' === $trace['class'] && 'processInlines' === $trace['function']) {
                        $node = $trace['args'][1];
                        break;
                    }
                };

                if ($node->getParent() instanceof ListItem && $node->getParent()->getParent()->isTight()) {
                    $node = $node->getParent();
                }
            } elseif ($previous) {
                $node = $previous;
            }

            if ($node) {
                $node->data['attributes'] = AttributesUtils::merge($node, $inline->getAttributes());
            }
        }
    }
}
