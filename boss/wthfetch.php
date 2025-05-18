<?php
// Remove redundant session_start() and ob_start() since they’re handled elsewhere
class APIClient
{
    private $api_url;

    public function __construct()
    {
        $this->api_url = "http://127.0.0.1:8000/api"; // Match local development server
    }

    public function callAPI($endpoint, $method = "GET", $data = [], $token = null)
    {
        $url = $this->api_url . $endpoint;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set headers
        $headers = ['Content-Type: application/json'];
        if ($token) {
            $headers[] = "Authorization: Bearer " . trim($token);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Handle methods
        if ($method === "POST") {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif (in_array($method, ["PUT", "PATCH", "DELETE"])) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return ['error' => curl_error($ch)];
        }

        curl_close($ch);

        return json_decode($response, true);
    }
}