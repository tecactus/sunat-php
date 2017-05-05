<?php

namespace Tecactus\Sunat;


use GuzzleHttp\Client;
use Tecactus\Sunat\Exception\InvalidRucException;
use Tecactus\Sunat\Exception\InvalidDniException;

class RUC
{
    protected $client;
    protected $baseUri;
    protected $apiToken;

    public function __construct($apiToken)
    {
        $this->baseUri = "https://tecactus.com/";
        $this->apiToken = $apiToken;
        $this->client = new Client(['base_uri' => $this->baseUri, 'verify' => false, 'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $this->apiToken]]);
    }

    public function getByRuc($ruc, $asArray = false)
    {
        if (!$this->validate($ruc)) {
            throw new InvalidRucException('RUC number seems not to be valid.');
        }
        $response = $this->client->request('POST', 'api/sunat/query/ruc', ['query' => [
            'ruc' => $ruc
        ]]);
        return json_decode($response->getBody()->getContents(), $asArray);
    }

    public function getByDni($dni, $asArray = false)
    {
        if (!$this->validateDni($dni)) {
            throw new InvalidDniException('DNI number seems not to be valid.');
        }
        $response = $this->client->request('POST', 'api/sunat/query/dni', ['query' => 'dni=' . $dni]);
        return json_decode($response->getBody()->getContents(), $asArray);
    }    

    public function validate($value)
    {
        $value = trim((string)$value);

        if (is_numeric($value)) {
            if (($valuelength = strlen($value)) == 8){
                $sum = 0;
                for ($i = 0; $i < $valuelength - 1; $i++){
                    $digit = $this->charAt($value, $i) - '0';
                    if ( $i==0 ) {
                        $sum += ($digit*2);
                    } else {
                        $sum += ($digit*($valuelength - $i));
                    }
                }
                $diff = $sum % 11;
                if ($diff == 1) $diff = 11;
                if ($diff + ($this->charAt($value, $valuelength - 1) - '0') == 11) {
                    return true;
                }
                return false;
            } elseif (($valuelength = strlen($value)) == 11){
                $sum = 0;
                $x = 6;
                for ($i = 0; $i < $valuelength - 1; $i++){
                    if ( $i == 4 ) {
                        $x = 8;
                    }
                    $digit = $this->charAt($value, $i) - '0';
                    $x--;
                    if ( $i==0 ) {
                        $sum += ($digit*$x);
                    } else {
                        $sum += ($digit*$x);
                    }
                }
                $diff = $sum % 11;
                $diff = 11 - $diff;
                if ($diff >= 10) {
                    $diff = $diff - 10;
                }
                if ($diff == $this->charAt($value, $valuelength - 1 ) - '0') {
                    return true;
                }
                return false; 
            }
        }
        return false;
    }

    protected function validateDni($value)
    {
        if (is_numeric($value)) {
            return strlen($value) == 8;
        }
        return false;
    }

    protected function charAt($string, $index){
        if($index < mb_strlen($string)){
            return mb_substr($string, $index, 1);
        }
        else{
            return -1;
        }
    }
}