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

use League\CommonMark\Event\DocumentParsedEvent;

final class AttributesListener
{
    private $processor;

    public function __construct()
    {
        $this->processor = new AttributesProcessor();
    }

    public function __invoke(DocumentParsedEvent $event): void
    {
        $document = $event->getDocument();
        $this->processor->processDocument($document);
    }
}
