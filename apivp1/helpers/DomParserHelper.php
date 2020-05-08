<?php

namespace apivp1\helpers;

use DOMDocument;
use DOMNode;

/**
 * Helper methods to parse DOM elements.
 *
 * Class DomParserHelper
 * @package app\helpers
 */
class DomParserHelper
{
    /**
     * Parse an HTML string into an array of nodes in the format that
     * Weixin Miniapps expect.
     *
     * https://developers.weixin.qq.com/miniprogram/en/dev/component/rich-text.html
     *
     * The array of nodes can be later used to render the content of a rich-text view.
     *
     * At 2019-07-10 CSS styling is not working on the weapp, styles need to be inlined
     * on each element, Tencent is supposed to fix this at some point, then we will be
     * able to apply styles using CSS on the front-end.
     *
     * @param $html
     * @return array|mixed
     */
    public static function parseIntoArray ($html)
    {
        if (!empty($html)) {

            // https://www.php.net/manual/en/class.domdocument.php
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

            $doc = new DOMDocument('1.0', 'utf-8');
            $doc->loadHTML($html);

            // An optional way to accept utf-8 encoding would be adding a head element with a charset tag
            // $head = '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head>';

            // DOMDocument adds a body tag, process all elements on the body tag
            // We are only interested in the content of the body tag ['children']
            return self::parseNode($doc->getElementsByTagName('body')[0])['children'];

        }

        // If HTML is empty return an empty array
        return [];
    }

    /**
     * Recursively parse all the HTML DOMNodes.
     *
     * https://www.php.net/manual/en/class.domnode.php
     *
     * @param DOMNode $node
     * @return array
     */
    protected static function parseNode (DOMNode $node)
    {
        $array = [];

        // If we have a text node return {'type':'text','text':'...'}
        if( strcmp($node->nodeName, '#text') === 0 ) {

            // todo find out why some " " fields don't match the empty() call, probably encoding
            // Get rid of no-break spaces U+00A0
            // Get rid of narrow no-break spaces U+00A0
            // Get rid of zero-width no-break spaces U+00A0
            $textContent = str_replace(
                array('\u00a0', '\u202f', '\ufeff'),
                '', $node->textContent
            );

            if (empty(trim($textContent))) {
                return [];
            }

            // Text node
            $array['type'] = 'text';
            $array['text'] = trim($textContent);

        } else {

            /*
             * If the node is not a text node
             *  [
             *      'name' => 'div',
             *      'attrs' => [
             *          'class' => '...',
             *          'style' => '...'
             *      ],
             *      'children' => [
             *          [...],
             *          [...],
             *      ]
             *  ]
             */
            $array['name'] = $node->nodeName;

            if ($node->hasAttributes())
            {
                $array['attrs'] = [];
                foreach ($node->attributes as $attr)
                {
                    $array['attrs'][$attr->nodeName] = $attr->nodeValue;
                }

                if (strcmp($node->nodeName, 'img') === 0) {

                    $array['attrs']['style'] = 'max-width: 100%; margin: 0.2rem 0;';
                    $array['attrs']['class'] = 'pg-rich-text-img';

                } elseif (strcmp($node->nodeName, 'p') === 0) {

                    $array['attrs']['style'] = 'margin: 0.2rem 0;';

                }
            }

            if ($node->hasChildNodes())
            {
                $array['children'] = [];
                foreach ($node->childNodes as $childNode) {

                    // Avoid adding empty nodes
                    $childArray = self::parseNode($childNode);
                    if (!empty($childArray)) {
                        $array['children'][] = $childArray;
                    }
                }
            }
        }

        // Remove empty text elements from the result set
        if (isset($array['name'])
            && strcmp($array['name'], 'p') === 0
            && count($array['children']) === 1
            && isset($array['children'][0]['type'])
            && strcmp($array['children'][0]['type'], 'text')
            && trim($array['children'][0]['text']) != '') {
            return [];
        }

        return $array;

    }
}
