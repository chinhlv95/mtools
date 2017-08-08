<?php
namespace App\Repositories\Api;

class ApiRepository implements ApiRepositoryInterface
{
    public function getApi($url, $timeout){
        if(!isset($timeout))
        {
            $timeout=30;
        }
        $curl = curl_init();
        curl_setopt ($curl, CURLOPT_URL, $url);
        curl_setopt ($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $curl_data = curl_exec ($curl);
        curl_close($curl);
        $data = json_decode($curl_data, true);
        return $data;
    }
}