<?php

use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;

// HTTP GET REQUEST
function getRequest( $uri ) {





    try {
        if( $uri === null || $uri === "" ) { return null; }

        $client = new GuzzleHttp\Client([ 'verify' => false ]);
        $result = $client->request('GET', $uri, []);

        //echo "status code : " . $result->getStatusCode() . "\n";

        if( $result->getStatusCode() == "200" ) {
            return $result->getBody();
        }



    } catch (ClientException $e) {
        echo Psr7\Message::toString($e->getRequest());
        echo Psr7\Message::toString($e->getResponse());
    }



}

// HTTP POST REQUEST
function postRequest( $uri, $postData ) {

  if( $uri === null || $uri === "" ) { return null; }

  $client = new GuzzleHttp\Client(['verify' => false ]);
  $result = $client->request('POST', $uri, [ 'json' => $postData ]);

  //echo "status code : " . $result->getStatusCode() . "\n";

  if( $result->getStatusCode() == "200" ) {
      return $result->getBody();
  }

  return null;

}