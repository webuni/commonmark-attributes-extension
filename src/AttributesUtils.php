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
use League\CommonMark\Cursor;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Util\RegexHelper;

final class AttributesUtils
{
    private static $regexp = '/^\s*([.#][_a-z0-9-]+|'.RegexHelper::PARTIAL_ATTRIBUTENAME.RegexHelper::PARTIAL_ATTRIBUTEVALUESPEC.')(?<!})\s*/i';

    public static function parse(Cursor $cursor): array
    {
        $state = $cursor->saveState();
        $cursor->advanceToNextNonSpaceOrNewline();
        if ('{' !== $cursor->getCharacter()) {
            $cursor->restoreState($state);

            return [];
        }

        $cursor->advanceBy(1);
        if (':' === $cursor->getCharacter()) {
            $cursor->advanceBy(1);
        }

        $attributes = [];
        while ($attribute = trim((string) $cursor->match(self::$regexp))) {
            if ('#' === $attribute[0]) {
                $attributes['id'] = substr($attribute, 1);

                continue;
            }

            if ('.' === $attribute[0]) {
                $attributes['class'][] = substr($attribute, 1);

                continue;
            }

            list($name, $value) = explode('=', $attribute, 2);
            $first = $value[0];
            $last = substr($value, -1);
            if ((('"' === $first && '"' === $last) || ("'" === $first && "'" === $last)) && strlen($value) > 1) {
                $value = substr($value, 1, -1);
            }

            if ('class' === strtolower(trim($name))) {
                foreach (array_filter(explode(' ', trim($value))) as $class) {
                    $attributes['class'][] = $class;
                }
            } else {
                $attributes[trim($name)] = trim($value);
            }
        }

        if (null === $cursor->match('/}/')) {
            $cursor->restoreState($state);

            return [];
        }

        if (!count($attributes)) {
            $cursor->restoreState($state);

            return [];
        }

        if (isset($attributes['class'])) {
            $attributes['class'] = implode(' ', (array) $attributes['class']);
        }

        return $attributes;
    }

    public static function merge($attributes1, $attributes2): array
    {
        $attributes = [];
        foreach ([$attributes1, $attributes2] as $arg) {
            if ($arg instanceof AbstractBlock || $arg instanceof AbstractInline) {
                $arg = $arg->data['attributes'] ?? [];
            }

            $arg = (array) $arg;
            if (isset($arg['class'])) {
                foreach (array_filter(explode(' ', trim($arg['class']))) as $class) {
                    $attributes['class'][] = $class;
                }
                unset($arg['class']);
            }
            $attributes = array_merge($attributes, $arg);
        }

        if (isset($attributes['class'])) {
            $attributes['class'] = implode(' ', $attributes['class']);
        }

        return $attributes;
    }
}
