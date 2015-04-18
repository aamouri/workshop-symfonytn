<?php

namespace Workshop\HttpDataCollectorBundle\Collector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class HttpDataCollector extends DataCollector
{
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            'request' => $request->__toString(),
            'response' => $response->__toString(),
            'curl_request' => $this->forgeRequest($request),
        );
    }

    private function forgeRequest(Request $request)
    {
        $curlRequest =
            'curl -X '.$request->getMethod().
            ' http://'.$request->server->get('HTTP_HOST').$request->getRequestUri()
        ;

        foreach ($request->headers as $name => $header) {
            $value = implode(' ', $header);
            $curlRequest .= ' -H "'.$name.': '.$value.'"';
        }

        return $curlRequest;
    }

    public function getRequest()
    {
        return $this->data['request'];
    }

    public function getResponse()
    {
        return $this->data['response'];
    }

    public function getCurlRequest()
    {
        if (isset($this->data['curl_request'])) {
            return $this->data['curl_request'];
        }
    }

    public function getName()
    {
        return 'http';
    }
}