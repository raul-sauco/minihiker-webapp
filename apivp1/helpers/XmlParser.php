<?php

namespace apivp1\helpers;

use Exception;
use yii\web\BadRequestHttpException;
use yii\web\RequestParserInterface;

/**
 * Class XmlParser
 * Parse an XML request body into a POST parameter array.
 *
 * @package apivp1\helpers
 */
class XmlParser implements RequestParserInterface
{
    public $asArray = true;
    public $throwException = true;

    /**
     * Parses a HTTP request body.
     * @param string $rawBody the raw HTTP request body.
     * @param string $contentType the content type specified for the request body.
     * @return array parameters parsed from the request body
     * @throws BadRequestHttpException if the body contains invalid xml and     [[throwException]] is `true`.
     */
    public function parse($rawBody, $contentType)
    {
        try {
            $parameters = simplexml_load_string($rawBody);
            if($this->asArray) {
                $parameters = (array) $parameters;
            }
            return $parameters ?? [];
        } catch (Exception $e) {
            if ($this->throwException) {
                throw new BadRequestHttpException(
                    'Invalid XML data in request body: ' . $e->getMessage());
            }
            return [];
        }
    }
}
