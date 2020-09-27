<?php

namespace DigitalHub\Automation\Model;

class Curl {

    /**
     * Execute CURL request and return the result if succeed
     * @param  string  $url
     * @param  string  $method
     * @param  array   $data
     * @param  string|false $auth
     * @return mixed
     */
    public function send($url, $method = 'GET', $data = null, $auth = false)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        if ($auth) {
            $headers[] = 'Authorization: Bearer ' . $auth;
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        if ($method == 'POST' || $method == 'PUT') {
            curl_setopt($curl, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        try {
            $result = curl_exec($curl);
            var_dump($result);exit;
            $object = json_decode($result);
            return $object;
        } catch (\Exception $e) {
            return false;
        }
    }
}
