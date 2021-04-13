<?php

use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

include_once 'bootstrap/autoload.php';


//Inicializa Cliente HTTP Guzzle
$client = new Client();

//Realiza Peticion para realizar un Pago
$data2 = $client->request('POST', 'https://contractvs.casanare.gov.co/webservice/WS_ConsultaContratista.php', [
    'verify' => false,
//$data2 = $client->request('POST', '127.0.0.1:8080/contractvs/webservice/WS_ConsultaContratista.php',[
    'json' => [
        'nit'     => '900364032'
    ],

]);

//Decodifica Json
$jsonPago = $data2->getBody()->getContents();

echo $jsonPago;
