<?php
// inc\APIClient.php

require_once 'config.php';

if (!class_exists('APIClient')) {
    class APIClient {
        public function callAPI($endpoint, $method = 'GET', $data = [], $token = null) {
            $url = API_BASE_URL . $endpoint;
            $curl = curl_init($url);

            $headers = [];
            if ($method === 'POST' && !empty($data)) {
                $jsonData = json_encode($data);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
                $headers[] = 'Content-Type: application/json';
                $headers[] = 'Content-Length: ' . strlen($jsonData);
            }
            if ($token) {
                $headers[] = "Authorization: Bearer {$token}";
            }

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            if (!empty($headers)) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            }
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if (curl_errno($curl)) {
                $error = curl_error($curl);
                curl_close($curl);
                return ['error' => $error];
            }

            $responseData = json_decode($response, true);
            curl_close($curl);
            return $responseData ?: ['status_code' => $httpCode, 'message' => 'Invalid response'];
        }
    }
}
?>