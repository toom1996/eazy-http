<?php

namespace eazy\http\filters;

use eazy\http\ActionFilter;
use eazy\http\App;
use eazy\http\BaseHook;
use eazy\http\BaseObject;
use eazy\http\Controller;
use eazy\http\exceptions\InvalidConfigException;
use eazy\http\Hook;

/**
 *  // restrict access to
 *  'Origin' => ['http://www.myserver.com', 'https://www.myserver.com'],
 *  // Allow only POST and PUT methods
 *  'Access-Control-Request-Method' => ['POST', 'PUT'],
 *  // Allow only headers 'X-Wsse'
 *  'Access-Control-Request-Headers' => ['X-Wsse'],
 *  // Allow credentials (cookies, authorization headers, etc.) to be exposed to the browser
 *  'Access-Control-Allow-Credentials' => true,
 *  // Allow OPTIONS caching
 *  'Access-Control-Max-Age' => 3600,
 *  // Allow the X-Pagination-Current-Page header to be exposed to the browser.
 *  'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
 */
class CorsFilter extends BaseHook
{
    public $cors = [
        'Origin' => ['*'],
        'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
        'Access-Control-Request-Headers' => ['*'],
        'Access-Control-Allow-Credentials' => null,
        'Access-Control-Max-Age' => 86400,
        'Access-Control-Expose-Headers' => [],
    ];

    #[Hook("hook.beforeAction")]
    public function beforeAction()
    {
//        $response = App::$locator->response;
//        $responseCorsHeaders = $this->prepareHeaders($requestCorsHeaders);
//        $this->addCorsHeaders($this->response, $responseCorsHeaders);
        $requestCorsHeaders = $this->extractHeaders();
        $responseCorsHeaders = $this->prepareHeaders($requestCorsHeaders);
//        $responseHeaders = [];
//        if (isset($this->cors['Access-Control-Allow-Headers'])) {
//            $responseHeaders['Access-Control-Allow-Headers'] = implode(', ', $this->cors['Access-Control-Allow-Headers']);
//        }
//
//        if (isset($this->cors['Access-Control-Request-Method'])) {
//            $responseHeaders['Access-Control-Allow-Methods'] = implode(', ', $this->cors['Access-Control-Request-Method']);
//        }


        App::$locator->response->setHeaders($responseCorsHeaders);
    }

    public function prepareHeaders($requestHeaders)
    {
        $request = App::$locator->request;
        $responseHeaders = [];
        // handle Origin
        if (isset($requestHeaders['Origin'], $this->cors['Origin'])) {
            if (in_array($requestHeaders['Origin'], $this->cors['Origin'], true)) {
                $responseHeaders['Access-Control-Allow-Origin'] = $requestHeaders['Origin'];
            }

            if (in_array('*', $this->cors['Origin'], true)) {
                // Per CORS standard (https://fetch.spec.whatwg.org), wildcard origins shouldn't be used together with credentials
                if (isset($this->cors['Access-Control-Allow-Credentials']) && $this->cors['Access-Control-Allow-Credentials']) {
//                    if (YII_DEBUG) {
//                        throw new InvalidConfigException("Allowing credentials for wildcard origins is insecure. Please specify more restrictive origins or set 'credentials' to false in your CORS configuration.");
//                    } else {
//                        Yii::error("Allowing credentials for wildcard origins is insecure. Please specify more restrictive origins or set 'credentials' to false in your CORS configuration.", __METHOD__);
//                    }
                    throw new InvalidConfigException("Allowing credentials for wildcard origins is insecure. Please specify more restrictive origins or set 'credentials' to false in your CORS configuration.");
                } else {
                    $responseHeaders['Access-Control-Allow-Origin'] = '*';
                }
            }
        }

        if (isset($requestHeaders['Access-Control-Request-Method'])) {
            $responseHeaders['Access-Control-Allow-Methods'] = implode(', ', $this->cors['Access-Control-Request-Method']);
        }

        if (isset($this->cors['Access-Control-Allow-Credentials'])) {
            $responseHeaders['Access-Control-Allow-Credentials'] = $this->cors['Access-Control-Allow-Credentials'] ? 'true' : 'false';
        }


        if (isset($this->cors['Access-Control-Max-Age']) && $request->getIsOptions()) {
            $responseHeaders['Access-Control-Max-Age'] = $this->cors['Access-Control-Max-Age'];
        }

        if (isset($this->cors['Access-Control-Expose-Headers'])) {
            $responseHeaders['Access-Control-Expose-Headers'] = implode(', ', $this->cors['Access-Control-Expose-Headers']);
        }

        if (isset($this->cors['Access-Control-Allow-Headers'])) {
            $responseHeaders['Access-Control-Allow-Headers'] = implode(', ', $this->cors['Access-Control-Allow-Headers']);
        }

        return $responseHeaders;
    }


    public function extractHeaders()
    {
        $headers = [];
        $requestHeader = App::$locator->request->getHeader();
        foreach (array_keys($this->cors) as $headerField) {
            $serverField = lcfirst($headerField);
            $headerData = isset($requestHeader[$serverField]) ? $requestHeader[$serverField] : null;
            if ($headerData !== null) {
                $headers[$headerField] = $headerData;
            }
        }

        return $headers;
    }
}