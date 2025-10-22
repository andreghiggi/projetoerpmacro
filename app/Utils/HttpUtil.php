<?php

namespace App\Utils;

use Illuminate\Http\Client\Response;

class HttpUtil {

    // @NOTE(Patric):
    // A PORCARIA do Laravel não guarda o body do Request
    // Dai temos que capturar o payload aqui também.
    // Eu odeio frameworks.

    public static function debug( Response $response, $payload = [] ): string {
        $url              = $response->effectiveUri();
        $method           = $response->transferStats->getRequest()->getMethod();

        $headers_list = [];
        foreach( $response->transferStats->getRequest()->getHeaders() as $k => $v ){
            $headers_list[] = "$k:$v[0]";
        }
        $request_headers  = implode("\n", $headers_list );
        $request_body     = json_encode($payload, JSON_PRETTY_PRINT);
        $response_code    = $response->status();
        $response_body    = $response->body();

        $headers_list = [];
        foreach( $response->headers() as $k => $v ){
            $headers_list[] = "$k:$v[0]";
        }
        $response_headers = implode("\n", $headers_list );

        $debug_string = "$method $url
$request_headers

$request_body

--- Response (code: $response_code) ---
$response_headers

$response_body

--- End ---

";
        return $debug_string;
    }

    //debug and die
    public static function dd(Response $response, $payload = []) {
        dd(static::debug($response, $payload));
    }

    //pretty print
    public static function pp(Response $response, $payload = []) {
        echo "<pre>";
        echo static::debug($response, $payload);
    }
}