<?php

/*
 * This is part of the webuni/commonmark-attributes-extension package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webuni\CommonMark\AttributesExtension\tests\functional;

use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use Webuni\CommonMark\AttributesExtension\AttributesExtension;
use Webuni\CommonMark\AttributesExtension\HtmlRenderer;
use Webuni\CommonMark\TableExtension\TableExtension;

class LocalDataTest extends \PHPUnit_Framework_TestCase
{
    private $parser;
    private $renderer;

    protected function setUp()
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new AttributesExtension());
        $environment->addExtension(new TableExtension());

        $this->parser = new DocParser($environment);
        $this->renderer = new HtmlRenderer($environment);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testExample($markdown, $html, $testName)
    {
        $documentAST = $this->parser->parse($markdown);
        $actualResult = $this->renderer->renderBlock($documentAST);

        $failureMessage = sprintf('Unexpected result for "%s" test', $testName);
        $failureMessage .= "\n=== markdown ===============\n".$markdown;
        $failureMessage .= "\n=== expected ===============\n".$html;
        $failureMessage .= "\n=== got ====================\n".$actualResult;

        $this->assertEquals($html, $actualResult, $failureMessage);
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        $ret = [];
        foreach (glob(__DIR__.'/data/*.md') as $markdownFile) {
            $testName = basename($markdownFile, '.md');
            $markdown = file_get_contents($markdownFile);
            $html = file_get_contents(__DIR__.'/data/'.$testName.'.html');

            $ret[] = [$markdown, $html, $testName];
        }

        return $ret;
    }
}
