<?php

namespace App\Service;

use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class YousignService 
{    
    private const PATHFILE = __DIR__ . '/../../public/pdf/';

    public function __construct(
        private HttpClientInterface $yousignClient,
        ){
    }
    //1- Initiate a Signature  Request
     function signatureRequest(): array
    {
      $response = $this->yousignClient->request(
        'POST' ,
        'signature_requests',
        [
            'body' => <<<JSON
            {
                "name": "Constat de Véhicule",
                "delivery_mode": "email",
                "timezone": "Europe/Paris"
            }
            JSON,
        'headers' => [
            'Content-Type' => 'application/json',
        ],
        ]
    );
    
    /*$statusCode = $response->getStatusCode();
    
    if($statusCode != 201){
        throw new  \Exception('Error while creating signature request');   
      }*/
    
    return $response->toArray();
    }
    
    //2- Uploder document
      function addDocumentToSignatureRequest(string $signatureRequestId, string $filename): array
    {
       
        $formFields = [
            'nature' => 'signable_document',
            'file' => DataPart::fromPath(self::PATHFILE . $filename, $filename,'application/pdf'),
        ];
    
        $formData = new FormDataPart($formFields);
        $headers = $formData->getPreparedHeaders()->toArray();
    
        $response = $this->yousignClient->request(
            'POST', 
             sprintf('signature_requests/%s/documents', $signatureRequestId), 
             [
               'headers' => $headers,
               'body' => $formData->bodyToIterable(),
             ]
        );
    
        /*$statusCode = $response->getStatusCode();
    
        if($statusCode != 201){
             throw new  \Exception('Error while creating signature request');   
    }*/
        return $response->toArray();
    }
    
    //3-Add a signer
     function addSignerToSignatureRequest(string $signatureRequestId,string $documentId,string $email,string $prenom,string $nom,): array
    {
        $response = $this->yousignClient->request(
            'POST', 
            sprintf('signature_requests/%s/signers', $signatureRequestId), 
            [
                'body' => <<<JSON
                       {
                           "info":{
                              "first_name":"$prenom",
                              "last_name":"$nom",
                              "email":"$email",
                              "locale":"fr"
                           },
                           "fields":[
                              {
                                 "type":"signature",
                                 "document_id": "$documentId",
                                 "page":1,
                                 "x":77,
                                 "y":581
                              }
                           ],
                           "signature_level": "electronic_signature",
                           "signature_authentication_mode": "no_otp"
                       }
                       JSON,
                'headers' => [
                'Content-Type' => 'application/json',
                ],
        ]);
    
       /* $statusCode = $response->getStatusCode();
    
        if($statusCode != 201){
             throw new  \Exception('Error while creating signature request');   
        }*/
        return $response->toArray();
    }
    
    //4- send the signature request
     function activateSignatureRequest(string $signatureRequestId): array
    {
        $response = $this->yousignClient->request(
            'POST', 
            sprintf('signature_requests/%s/activate', $signatureRequestId)
        );
        
       /* $statusCode = $response->getStatusCode();
    
        if($statusCode != 201){
             throw new  \Exception('Error while creating signature request');   
        }*/
    
        return $response->toArray();
    }  

 }
