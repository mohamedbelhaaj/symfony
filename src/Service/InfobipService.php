<?php
namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

class InfobipService
{
    private $apiKey;
    private $sender;

    public function __construct()
    {
        $this->apiKey = $_ENV['INFOBIP_API_KEY'];;
        $this->sender = $_ENV['INFOBIP_SENDER'];;
    }

    public function sendSms(string $phoneNumber, string $message)
    {
        $httpClient = HttpClient::create();

        $response = $httpClient->request('POST', 'https://api.infobip.com/sms/2/text/single', [
            'headers' => [
                'Authorization' => 'App ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'from' => $this->sender,
                'to' => $phoneNumber,
                'text' => $message,
            ],
        ]);

        // Handle the response as needed
        $statusCode = $response->getStatusCode();
        $content = $response->toArray();

        return ['status' => $statusCode, 'response' => $content];
    }
}
?>