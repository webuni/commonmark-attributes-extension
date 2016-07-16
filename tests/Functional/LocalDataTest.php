<?php

/*
 * This is part of the webuni/commonmark-attributes-extension package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webuni\CommonMark\AttributesExtension\Tests\Functional;

use League\CommonMark\Converter;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use Webuni\CommonMark\AttributesExtension\AttributesExtension;
use Webuni\CommonMark\TableExtension\TableExtension;

class LocalDataTest extends \PHPUnit_Framework_TestCase
{
    protected $converter;

    protected function setUp()
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new AttributesExtension());
        $environment->addExtension(new TableExtension());

        $this->converter = new Converter(new DocParser($environment), new HtmlRenderer($environment));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testExample($markdown, $html, $testName)
    {
        $actualResult = $this->converter->convertToHtml($markdown);

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
